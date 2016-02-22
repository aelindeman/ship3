<?php

return [

	'app' => 'Ship',
	'translation-credit' => 'Traduction française par <a href="http://ael.me/">Alex Lindeman</a>',

	'header' => [
		'toolbar' => [
			'time-period' => 'Période',
			'autoreload' => [
				'enable' => 'Activer rechargement automatique',
				'disable' => 'Désactiver rechargement automatique'
			],
			'dark-mode' => [
				'enable' => 'Mode nuit',
				'disable' => 'Mode jour'
			]
		]
	],

	'errors' => [
		'no-components' => [
			'header' => 'Pas des composants actives',
			'description' => 'Ship ne pouvait pas trouver rien de composants d’afficher. Consultez <a href="http://github.com/aelindeman/ship/wiki/no-components">le wiki Ship</a> pour l’aide.'
		],
		'not-found' => [
			'title' => 'Pas trouvé',
			'header' => 'Cette page est vide.',
			'link' => 'Retourner à page d’aperçu'
		]
	],

	'units' => [
		'bytes' => [
			'abbr' => 'o',
			'name' => 'octet|octets'
		]
	],

	'time' => [
		'formats' => [
			'date' => 'M-j',
			'time' => 'H\hi'
		],
		'relative' => [
			'next' => ':value prochaines :units',
			'previous' => ':value dernières :units'
		],
		'minute' => 'minute|minutes',
		'hour'   => 'heure|heures',
		'day'    => 'jour|jours'
	],

	'footer' => [
		'slogan' => 'Alimenté par Ship'
	]

];
