<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_PROCESSES', true),

	/*
	 * Where does this component go on the overview page?
	 */
	'order' => env('COMPONENT_PROCESSES_ORDER', 7),

	/*
	 * The number of processes to show in each category
	 */
	'count' => env('COMPONENT_PROCESSES_COUNT', 3),

	/*
	 * The number of processes to show in each category
	 */
	'executable' => env('COMPONENT_PROCESSES_EXECUTABLE', 'ps'),

	/*
	 * Arument list to use.
	 *   linux:   ps -e -o ...
	 *   freebsd: ps -a -c -o ...
	 */
	'args' => env('COMPONENT_PROCESSES_ARGS')

];
