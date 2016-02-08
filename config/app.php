<?php

/*
|--------------------------------------------------------------------------
| Ship Configuration
|--------------------------------------------------------------------------
|
| Any preferences you change here might be overwritten the next time that
| Ship is updated. It is suggested that you instead open a text editor,
| copy .env.example to .env, and edit your preferences there instead.
|
*/

return [

	/*
	 * Change the title of Ship here. It is this machine's
	 *   hostname by default.
	 */
	'title' => env('SHIP_TITLE', gethostname()),

	/*
	 * Default appearance can be set to either dark or light mode.
	 */
	'dark-mode' => env('SHIP_DARK_MODE', false),

	/*
	 * How many hours' worth of data should graphs show by default?
	 */
	'graph-width' => env('SHIP_GRAPH_WIDTH', 3),

	/*
	 * Should components automatically refresh their data?
	 */
	'autoreload' => env('SHIP_AUTORELOAD', true),

	/*
	 * Activates debug mode.
	 */
	'debug' => env('APP_DEBUG', false),

	/*
	 * Choose a language here, or specify one in the .env file.
	 */
	'locale' => env('APP_LOCALE', 'en'),
	'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

	/*
	 * Encryption keys.
	 * These aren't used by Ship, but they are necessary for the underlying
	 *   framework (Lumen), so don't remove them.
	 * (You should have an APP_KEY entry in your .env file; the one here should
	 *   never be used.)
	 */
	'key' => env('APP_KEY', 'SomeRandomKey!!!'),
	'cipher' => 'AES-256-CBC'

];
