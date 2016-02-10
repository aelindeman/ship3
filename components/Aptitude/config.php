<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_APTITUDE', true),

	/*
	 * Where does this component go on the overview page?
	 */
	'order' => env('COMPONENT_APTITUDE_ORDER', 9),

	/*
	 * Number of minutes to cache the package info for.
	 * apt-get can take a couple seconds to run, so caching its output
	 *   drastically improves performance.
	 */
	'cache' => env('COMPONENT_APTITUDE_CACHE', 1440),

	/*
	 * Fetch package versions too?
	 * If set to false Aptitude will only return the number of updates
	 *   available. (This won't really improve performance, unfortunately.)
	 */
	'packages' => env('COMPONENT_APTITUDE_PACKAGES', true),

];
