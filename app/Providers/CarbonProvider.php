<?php

namespace App\Providers;

use App\Controllers\ComponentController;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class CarbonProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register Carbon inside the app container.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('carbon', function ($app) {
			Carbon::setLocale(config('app.locale'));
			return new Carbon();
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['carbon' => Carbon::class];
	}
}
