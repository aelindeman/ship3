<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_NETADDRESS', true),

	/*
	 * Number of minutes to cache the IP addreses for.
	 * NetAddress pings icanhazip.com to check the server's external IP, and
	 *   caches the address as to not spam the server.
	 */
	'cache' => env('COMPONENT_NETADDRESS_CACHE', 360),

	/*
	 * Check for IPv4 address?
	 */
	'ipv4' => env('COMPONENT_NETADDRESS_IPv4', true),

	/*
	 * Check for IPv6 address?
	 */
	'ipv6' => env('COMPONENT_NETADDRESS_IPv6', true),

];
