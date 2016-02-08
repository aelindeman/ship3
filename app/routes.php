<?php

use App\Controllers\ComponentController;

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
	return view('home');
});

// JSON and JSONP endpoint
$app->get('/json', function() use ($app) {
	$response = response()->json(app(ComponentController::class)->getData());
	if ($cb = $app->request->input('callback')) {
		try {
			$response->setCallback($cb);
		} catch (\InvalidArgumentException $e) {
			return response()->json([$e->getMessage()], 400);
		}
	}
	return $response;
});

// Version number
$app->get('/version', function() use ($app) {
	return $app->version();
});
