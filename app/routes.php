<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->group(['namespace' => 'App\\Helpers'], function() use ($app) {

	// Home view
	$app->get('/', 'RouteHelper@overviewPage');

	// JSON(P)
	// (Why aren't namespaces inherited?)
	$app->group(['namespace' => 'App\\Helpers', 'prefix' => 'json'], function() use ($app) {

		// All components
		$app->get('', 'RouteHelper@generateJSON');
		$app->get('diff', 'RouteHelper@generateDifferenceJSON');
		$app->get('graph', 'RouteHelper@generateGraphJSON');

		// Single component
		$app->get('component/{component}', 'RouteHelper@generateJSON');
		$app->get('component/{component}/diff', 'RouteHelper@generateDifferenceJSON');
		$app->get('component/{component}/graph', 'RouteHelper@generateGraphJSON');

	});

	// Trigger Javascript initialization (language files, configuration, etc.)
	$app->get('/init', 'RouteHelper@initJS');

});

// Version number
$app->get('/version', function() use ($app) {
	return response()->json(['version' => $app->version()]);
});
