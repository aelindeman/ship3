<?php

namespace App\Helpers;
use App\Controllers\ComponentController;

use DateInterval;

class OverviewHelper
{
	/**
	 * Retrieves component data and renders it as JSON.
	 * @param $component string Retrieve data for a single component
	 * @return Response
	 */
	public static function generateJSON($component = null)
	{
		$response = response();

		try {
			$cc = app(ComponentController::class)->run($component);
			static::handleFlushCacheRequest($cc, $component);

			$data = $cc->getData()->sortBy('order');

			if ($component) {
				$data = $data->get($component);
			}

			$response = $response->json($data)
				->setCallback(app('request')->input('callback', null));

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
	public static function generateGraphJSON($component = null)
	{
		$response = response();

		try {
			$cc = app(ComponentController::class)->run($component);
			static::handleFlushCacheRequest($cc, $component);

			$period = new DateInterval(
				app('request')->input('period', 'PT'.config('ship.graph-width'))
			);

			$data = $cc->getGraphData($period);

			$response = $response->json($data)
				->setCallback(app('request')->input('callback', null));

		} catch (\Exception $e) {
			$response = $response->json(['error' => $e->getMessage()], 400);
		}

		return $response;
	}

	/**
	 * Generates the 'overview' page, displaying all components.
	 * @return Response
	 */
	public static function overviewPage()
	{
		$cc = app(ComponentController::class)->run();
		static::handleFlushCacheRequest($cc);

		return view('home', [
			'components' => $cc->getData()
		]);
	}

	/**
	 * Registers components to get their configuration and translations, so
	 *   they can be passed to Javascript.
	 * Response is generated as a JSONP callback, which calls ShipJS.init by
	 *   default.
	 * @return Response ShipJS.init JSONP callback
	 */
	public static function initJS()
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
