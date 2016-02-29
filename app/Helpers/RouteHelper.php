<?php

namespace App\Helpers;
use App\Controllers\ComponentController;

use Carbon\Carbon;
use DateInterval;

class RouteHelper
{
	protected $callback  = false;
	protected $diffPeriod;
	protected $diffFrom;

	public function __construct()
	{
		$this->callback = app('request')->input('callback', null);

		$this->diffPeriod = new DateInterval(
			app('request')->input('period', config('ship.period'))
		);

		$this->diffFrom = app('request')->input('from') ?
			Carbon::parse(app('request')->input('from')) :
			Carbon::now();
	}

	/**
	 * Retrieves component data and renders it as JSON.
	 * @param $component string Retrieve data for a single component
	 * @return Response
	 */
	public function generateJSON($component = null)
	{
		$response = response();

		try {
			$cc = app(ComponentController::class)->run($component);
			static::handleFlushCacheRequest($cc, $component);

			$data = $cc->getProcessedData($this->diffPeriod, $this->diffFrom)
				->sortBy('order');

			if ($component) {
				$data = $data->get($component);
			}

			$response = $response->json($data)->setCallback($this->callback);

		} catch (\Exception $e) {
			$response = $response->json(['error' => $e->getMessage()], 400);
		}

		return $response;
	}

	/**
	 * Generates graph data for components and renders it as JSON.
	 * @param $component string Generate data for a single component
	 * @return Response
	 */
	public function generateDifferenceJSON($component = null)
	{
		$response = response();

		try {
			$cc = app(ComponentController::class)->run($component);
			static::handleFlushCacheRequest($cc, $component);

			$data = $cc->getDifferenceData($this->diffPeriod, $this->diffFrom);

			if ($component) {
				$data = $data->get($component);
			}

			$response = $response->json($data)->setCallback($this->callback);

		} catch (\Exception $e) {
			$response = $response->json(['error' => $e->getMessage()], 400);
		}

		return $response;
	}

	/**
	 * Generates graph data for components and renders it as JSON.
	 * @param $component string Generate data for a single component
	 * @return Response
	 */
	public function generateGraphJSON($component = null)
	{
		$response = response();

		try {
			$cc = app(ComponentController::class)->run($component);
			static::handleFlushCacheRequest($cc, $component);

			$data = $cc->getGraphData($this->diffPeriod);

			if ($component) {
				$data = $data->get($component);
			}

			$response = $response->json($data)->setCallback($this->callback);

		} catch (\Exception $e) {
			$response = $response->json(['error' => $e->getMessage()], 400);
		}

		return $response;
	}

	/**
	 * Generates the 'overview' page, displaying all components.
	 * @return Response
	 */
	public function overviewPage()
	{
		$cc = app(ComponentController::class)->run();
		static::handleFlushCacheRequest($cc);

		$data = $cc->getProcessedData($this->diffPeriod, $this->diffFrom)	
			->sortBy('order');

		return view('home', [
			'components' => $data
		]);
	}

	/**
	 * Registers components to get their configuration and translations, so
	 *   they can be passed to Javascript.
	 * Response is generated as a JSONP callback, which calls ShipJS.init by
	 *   default.
	 * @return Response ShipJS.init JSONP callback
	 */
	public function initJS()
	{
		try {
			$registered = app(ComponentController::class)->registerComponents();

			$init = [
				'config' => static::getConfiguration($registered),
				'lang' => static::getTranslations($registered)
			];

			$callback = app('request')->input('callback', 'ShipJS.init');

			return response()->json($init)
				->setCallback($callback)
				->withHeaders([
					'Cache-Control' => 'public, max-age=86400',
				]);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	/**
	 * Returns Ship and component translations.
	 * @param $components array List of components to get translations for
	 * @return array Array of translations
	 */
	protected static function getTranslations($components)
	{
		// get core translations
		$lang = collect([
			'ship' => app('translator')->get('ship')
		]);

		// get translations for components
		foreach ($components as $class => $path) {
			$name = app(ComponentController::class)->getComponentName($class);
			if (app('translator')->has($name.'::component')) {
				$lang->put($name, app('translator')->get($name.'::component'));
			}
		}

		return $lang->toArray();
	}

	/**
	 * Returns Ship and component configuration.
	 * @param $components array List of components to get configuration for
	 * @return array Configuration array
	 */
	protected static function getConfiguration($components)
	{
		return [
			'ship' => config('ship'),
			'components' => config('components')
		];
	}

	/**
	 * Handle cache=no input on any request.
	 * @param &$componentController ComponentController
	 * @param $component
	 * @return ComponentController
	 */
	protected static function handleFlushCacheRequest(&$componentController, $component = null)
	{
		return (app('request')->input('cache') == 'no') ?
			$componentController->flush()->run($component) :
			$componentController;
	}
}
