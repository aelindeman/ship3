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
	'graph-width' => env('SHIP_GRAPH_WIDTH', '3H'),

	/*
	 * Should components automatically refresh their data?
	 */
	'autoreload' => env('SHIP_AUTORELOAD', true)

];
