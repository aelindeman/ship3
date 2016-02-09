<?php

use App\Controllers\ComponentController;
use App\Exceptions\ComponentNotFoundException;

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
	return view('home', [
		'components' => app(ComponentController::class)->run()->getData()
	]);
});

// JSON and JSONP endpoint
$app->get('/json', function() use ($app) {
	try {
		$cc = app(ComponentController::class)->run();

		if ($app->request->input('cache') == 'no') {
			$cc->flush()->run();
		}

		$response = response()->json($cc->getData());
	} catch (\Exception $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	}

	if ($cb = $app->request->input('callback')) {
		try {
			$response->setCallback($cb);
		} catch (\InvalidArgumentException $e) {
			return response()->json(['error' => $e->getMessage()], 400);
		}
	}

	return $response;
});

// JSON and JSONP for single components
$app->get('/json/{component}', function($component) use ($app) {
	try {
		$cc = app(ComponentController::class)->runOne($component);

		if ($app->request->input('cache') == 'no') {
			$cc->flush()->runOne($component);
		}

		$response = response()->json($cc->getData());
	} catch (ComponentNotFoundException $e) {
		return response()->json(['error' => $e->getMessage()], 404);
	} catch (\Exception $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	}

	if ($cb = $app->request->input('callback')) {
		try {
			$response->setCallback($cb);
		} catch (\InvalidArgumentException $e) {
			return response()->json(['error' => $e->getMessage()], 400);
		}
	}

	return $response;
});

// Version number
$app->get('/version', function() use ($app) {
	return $app->version();
});
