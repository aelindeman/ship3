<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_APTITUDE', true),

	/*
	 * Number of minutes to cache the package info for.
	 * apt-get can take a couple seconds to run, so caching its output
	 *   drastically improves performance.
	 */
	'cache' => env('COMPONENT_APTITUDE_CACHE', 1440),

];
