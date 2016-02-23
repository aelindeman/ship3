<?php

namespace App;
use App\Controllers\ComponentController;
use App\Exceptions\ComponentNotFoundException;

use DateInterval;

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

		return addJsonCallbackOrFail($response);
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

		return addJsonCallbackOrFail($response);
	});

	// Graph data for all components
	$app->get('graph', function() use ($app) {
		try {
			$cc = app(ComponentController::class)->run();

			if ($app->request->input('cache') == 'no') {
				$cc->flush()->run();
			}

			$period = $app->request->input('period') ?
				new DateInterval($app->request->input('period')) :
				new DateInterval('PT'.config('app.graph-width'));

			$limit = $app->request->input('limit');

			$data = $cc->getGraphData($period, $limit);
			$response = response()->json($data);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

		return addJsonCallbackOrFail($response);
	});

	// Graph data for a specific component
	$app->get('component/{component}/graph', function($component) use ($app) {
		try {
			$cc = app(ComponentController::class)->run($component);

			if ($app->request->input('cache') == 'no') {
				$cc->flush()->run($component);
			}

			$period = $app->request->input('period') ?
				new DateInterval($app->request->input('period')) :
				new DateInterval('PT'.config('app.graph-width'));

			$limit = $app->request->input('limit');

			$data = $cc->getGraphData($period, $limit)->get($component);
			$response = response()->json($data);
		} catch (ComponentNotFoundException $e) {
			return response()->json(['error' => $e->getMessage()], 404);
		} catch (\DomainException $e) {
			return response()->json(['error' => $e->getMessage()], 400);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}

		return addJsonCallbackOrFail($response);
	});

});

// Language files for Javascript
$app->get('/lang', function() use ($app) {
	try {
		$registered = app(ComponentController::class)->registerComponents();

		$lang = collect();
		$lang->put('ship', $app->translator->get('ship'));

		foreach ($registered as $class => $path) {
			$name = app(ComponentController::class)->getComponentName($class);
			if ($app->translator->has($name.'::component')) {
				$lang->put($name, $app->translator->get($name.'::component'));
			}
		}

		return response()->json($lang)
			->setCallback('ShipJS.registerLang')
			->withHeaders([
				'Cache-Control' => 'public, max-age=86400',
			]);
	} catch (\Exception $e) {
		return response()->json(['error' => $e->getMessage()], 500);
	}
});

// Version number
$app->get('/version', function() use ($app) {
	return response()->json(['version' => $app->version()]);
});
