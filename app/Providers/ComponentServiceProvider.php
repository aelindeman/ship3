<?php

namespace App\Providers;

use App\Controllers\ComponentController;
use Illuminate\Support\ServiceProvider;

class ComponentServiceProvider extends ServiceProvider
{
	/**
	 * Register the ComponentController inside the app container.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(ComponentController::class, function ($app) {
			return new ComponentController();
		});
	}
}
