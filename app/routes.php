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

// Home view
$app->get('/', 'App\\Helpers\\OverviewHelper@overviewPage');

// JSON(P)
$app->group(['prefix' => 'json'], function() use ($app) {
	$app->get('', 'App\\Helpers\\OverviewHelper@generateJSON');
	$app->get('component/{component}', 'App\\Helpers\\OverviewHelper@generateJSON');
	$app->get('graph', 'App\\Helpers\\OverviewHelper@generateGraphJSON');
	$app->get('component/{component}/graph', 'App\\Helpers\\OverviewHelper@generateGraphJSON');
});

// Trigger Javascript initialization (language files, configuration, etc.)
$app->get('/init', 'App\\Helpers\\OverviewHelper@initJS');

// Version number
$app->get('/version', function() use ($app) {
	return response()->json(['version' => $app->version()]);
});
