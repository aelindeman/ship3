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
	 * Change the title of Ship here. It is this machine's hostname by default.
	 */
	'title' => env('SHIP_TITLE', gethostname()),

	/*
	 * Default appearance can be set to either dark or light mode.
	 */
	'dark-mode' => env('SHIP_DARK_MODE', false),

	/*
	 * How much data should graphs show by default?
	 * The syntax of this setting follows PHP's DateInterval syntax.
	 */
	'period' => env('SHIP_PERIOD', 'PT3H'),

	/*
	 * Should components automatically refresh their data?
	 */
	'autoreload' => env('SHIP_AUTORELOAD', true)

];
