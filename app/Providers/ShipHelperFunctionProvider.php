<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ShipHelperFunctionProvider extends ServiceProvider
{
	/**
     * Register the service provider.
     *
     * @return void
     */
	public function register()
	{
		//
	}

	/**
     * Bootstrap any application services.
     *
     * @return void
     */
	public function boot()
	{
		view()->composer('*', function() {
			include_once base_path('/app/functions.php');
		});
	}
}
