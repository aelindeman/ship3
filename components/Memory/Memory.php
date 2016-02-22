<?php

namespace App\Components;
use App\Behaviors\Graphable;
use App\Models\Component;

use Carbon\Carbon;

class Memory extends Component implements Graphable
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

	public static function series(\DateInterval $period = null, $limit = null)
	{
		$since = $period ?
			Carbon::now()->sub($period) :
			Carbon::now()->subHours(config('app.graph-width'));

		$data = static::where('time', '>=', $since)
			->orderBy('time', 'asc')
			->take($limit)
			->get();

		$used = $data->map(function($entry, $index) {
			return [
				'x' => Carbon::parse($entry->time)->timestamp,
				'y' => $entry->used,
			];
		});

		return [$used];
	}
}
