<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_NETTRAFFIC', true),

	/*
	 * The network interfaces to use as they appear in ifconfig, as a comma-
	 *   separated list.
	 */
	'interfaces' => env('COMPONENT_NETTRAFFIC_INTERFACES', 'eth0')

];
