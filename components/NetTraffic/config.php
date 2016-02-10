<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_NETTRAFFIC', true),

	/*
	 * Where does this component go on the overview page?
	 */
	'order' => env('COMPONENT_NETTRAFFIC_ORDER', 5),

	/*
	 * The network interfaces to use as they appear in ifconfig, as a comma-
	 *   separated list.
	 */
	'interfaces' => env('COMPONENT_NETTRAFFIC_INTERFACES', 'eth0')

];
