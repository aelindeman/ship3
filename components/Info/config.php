<?php

return [

	/*
	 * Enable this component?
	 */
	'enabled' => env('COMPONENT_INFO', true),

	/*
	 * Where does this component go on the overview page?
	 */
	'order' => env('COMPONENT_INFO_ORDER', 1),

	'uptime' => [

		/*
		 * Uptime format
		 */
		'format' => env('COMPONENT_INFO_UPTIME_FORMAT', '@d @h:@m:@s'),

	]

];
