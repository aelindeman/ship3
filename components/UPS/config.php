<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_UPS', true),

	/*
	 * Where does this component go on the overview page?
	 */
	'order' => env('COMPONENT_UPS_ORDER', 8),

	/*
	 * Path to the apcaccess binary. The component will try its best to
	 *   autodetect from $PATH, but if it doesn't work, you need to
	 *   specify it manually.
	 */
	'bin' => env('COMPONENT_UPS_BIN'),

	/*
	 * Host to fetch data from. If unset, it will default to localhost. The
	 *   host must also be set up properly in the apcaccess configuration.
	 */
	'host' => env('COMPONENT_UPS_HOST')

];
