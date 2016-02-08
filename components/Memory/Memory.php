<?php

namespace App\Components;
use App\Models\Component;

class Memory extends Component
{
	protected $table = 'memory';
	protected $fillable = [
		'free',
		'used',
		'cached',
		'total'
	];

	public static function fetch()
	{
		return file_get_contents('/proc/meminfo');
	}

	public static function parse($input = null)
	{
		// parse input
		foreach (explode(PHP_EOL, $input) as $r) {
			$r = preg_split('/\:\s+/', $r);
			if (in_array($r[0], ['MemTotal', 'MemFree', 'Cached', 'MemAvailable'])) {
				$m[$r[0]] = (int) preg_replace ('/[^0-9]/', null, $r[1]);
			}
		}

		// MemAvailable only exists on kernel 3.2 and newer,
		// so fall back to free + cached if it doesn't exist
		if (!array_key_exists('MemAvailable', $m)) {
			$m['MemAvailable'] = $m['MemFree'] + $m['Cached'];
		}

		$used = $m['MemTotal'] - $m['MemAvailable'];
		return [
			'free'   => (int)$m['MemAvailable'],
			'used'   => (int)$used,
			'cached' => (int)$m['Cached'],
			'total'  => (int)$m['MemTotal']
		];
	}
}
