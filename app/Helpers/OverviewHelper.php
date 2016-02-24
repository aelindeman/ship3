<?php

namespace App\Helpers;
use App\Controllers\ComponentController;

class OverviewHelper
{
	/**
	 * Returns Ship and component configuration and translations as a callback
	 *   to initialize ShipJS.
	 */
	public static function initJS()
	{
		try {
			$registered = app(ComponentController::class)->registerComponents();

			$init = [
				'config' => static::getConfiguration($registered),
				'lang' => static::getTranslations($registered)
			];

			return response()->json($init)
				->setCallback('ShipJS.init')
				->withHeaders([
					'Cache-Control' => 'public, max-age=86400',
				]);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	/**
	 * Returns Ship and component translations.
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

		return $lang;
	}

	/**
	 * Returns Ship and component configuration.
	 */
	protected static function getConfiguration($components)
	{
		return [
			'ship' => config('ship'),
			'components' => config('components')
		];
	}
}
