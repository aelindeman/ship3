<?php

use App\Controllers\ComponentController;
use App\Exceptions\ComponentNotFoundException;

/**
 * Quick helper function for adding JSONP/callback support to responses.
 */
function addCallbackOrFail(&$response)
{
	if ($cb = app('request')->input('callback')) {
		try {
			$response->setCallback($cb);
		} catch (\InvalidArgumentException $e) {
			$response = response()->json(['error' => $e->getMessage()], 400);
		}
	}
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
	return view('home', [
		'components' => app(ComponentController::class)->run()->getData()
	]);
});

// JSON(P)
$app->group(['prefix' => 'json'], function() use ($app) {

	// Data for all components
	$app->get('', function() use ($app) {
		try {
			$cc = app(ComponentController::class)->run();

			if ($app->request->input('cache') == 'no') {
				$cc->flush()->run();
			}

			// sort by order, then remove the order key
			$data = $cc->getData()->sortBy('order')->map(function($e) {
				unset($e['order']);
				return $e;
			});
			$response = response()->json($data);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

		addCallbackOrFail($response);
		return $response;
	});

	// Data for a specific component
	$app->get('component/{component}', function($component) use ($app) {
		try {
			$cc = app(ComponentController::class)->run($component);

			if ($app->request->input('cache') == 'no') {
				$cc->flush()->runOne($component);
			}

			$data = collect($cc->getData()->get($component))->except('order');
			$response = response()->json($data);
		} catch (ComponentNotFoundException $e) {
			return response()->json(['error' => $e->getMessage()], 404);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

		addCallbackOrFail($response);
		return $response;
	});

	// Graph data for all components
	$app->get('graph', function() use ($app) {
		try {
			$cc = app(ComponentController::class)->run();

			if ($app->request->input('cache') == 'no') {
				$cc->flush()->run();
			}

			$data = $cc->getGraphData();
			$response = response()->json($data);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

		addCallbackOrFail($response);
		return $response;
	});

	// Graph data for a specific component
	$app->get('component/{component}/graph', function($component) use ($app) {
		try {
			$cc = app(ComponentController::class)->run($component);

			if ($app->request->input('cache') == 'no') {
				$cc->flush()->run($component);
			}

			$data = $cc->getGraphData()->get($component);
			$response = response()->json($data);
		} catch (ComponentNotFoundException $e) {
			return response()->json(['error' => $e->getMessage()], 404);
		} catch (\DomainException $e) {
			return response()->json(['error' => $e->getMessage()], 400);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

		addCallbackOrFail($response);
		return $response;
	});

});

// Version number
$app->get('/version', function() use ($app) {
	return response()->json(['version' => $app->version()]);
});
