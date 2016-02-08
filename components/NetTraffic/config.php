<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_NETTRAFFIC', true),

	/*
	 * List of network interfaces to use, as an array,
	 * as they appear in ifconfig.
	 */
	'interfaces' => env('COMPONENT_NETTRAFFIC_INTERFACES', ['eth0'])

];
