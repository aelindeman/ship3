<?php

return [

	'app' => 'Ship',
	'translation-credit' => 'English translation by <a href="http://ael.me/">Alex Lindeman</a>',

	'header' => [
		'toolbar' => [
			'graph-width' => 'Graph width',
			'autoreload' => [
				'enable' => 'Enable autoreload',
				'disable' => 'Disable autoreload'
			],
			'dark-mode' => [
				'enable' => 'Dark mode',
				'disable' => 'Light mode'
			]
		]
	],

	'errors' => [
		'no-components' => [
			'header' => 'No active components',
			'description' => 'Ship couldn’t find any components to display. Check <a href="http://github.com/aelindeman/ship/wiki/no-components">the Ship wiki</a> for help.'
		],
		'not-found' => [
			'title' => 'Not found',
			'header' => 'Nothing to see here.',
			'link' => 'Return to overview page'
		]
	],

	'time' => [
		'formats' => [
			'date' => 'M/j',
			'time' => 'H:i'
		],
		'minute' => 'minute|minutes',
		'hour'   => 'hour|hours',
		'day'    => 'day|days'
	],

	'footer' => [
		'slogan' => 'Powered by Ship'
	],

];
