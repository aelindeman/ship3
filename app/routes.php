<?php

use App\Controllers\ComponentController;
use App\Exceptions\ComponentNotFoundException;

/**
 * Quick helper function for adding JSONP/callback support to responses.
 */
function addJsonCallbackOrFail($response)
{
	if ($cb = app('request')->input('callback')) {
		try {
			$response->setCallback($cb);
		} catch (\InvalidArgumentException $e) {
			$response = response()->json(['error' => $e->getMessage()], 400);
		}
	}

	return $response;
}

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
$app->get('/', function() use ($app) {
	$cc = app(ComponentController::class)->run();

	if ($app->request->input('cache') == 'no') {
		$cc->flush()->run($component);
	}

	return view('home', [
		'components' => $cc->getData()
	]);
});

// JSON(P)
$app->group(['prefix' => 'json'], function() use ($app) {
	$app->get('', 'App\\Helpers\\OverviewHelper@generateJSON');
	$app->get('{component}', 'App\\Helpers\\OverviewHelper@generateJSON');
	$app->get('graph', 'App\\Helpers\\OverviewHelper@generateGraphJSON');
	$app->get('component/{component}/graph', 'App\\Helpers\\OverviewHelper@generateGraphJSON');
});

// Trigger Javascript initialization (language files, configuration, etc.)
$app->get('/init', 'App\\Helpers\\OverviewHelper@initJS');

// Version number
$app->get('/version', function() use ($app) {
	return response()->json(['version' => $app->version()]);
});
