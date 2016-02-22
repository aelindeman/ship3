<?php

namespace App\Components;
use App\Behaviors\Graphable;
use App\Models\Component;

use Carbon\Carbon;

class LoadAverage extends Component implements Graphable
{
	protected $table = 'load';
	protected $fillable = [
		'one',
		'five',
		'fifteen'
	];

	public static function fetch()
	{
		return file_get_contents('/proc/loadavg');
	}

	public static function parse($input = null)
	{
		$p = preg_split ('/([\s]+|\/)/', $input);
		return [
			'one'     => (float)$p[0],
			'five'    => (float)$p[1],
			'fifteen' => (float)$p[2]
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

		$five = $data->map(function($entry, $index) {
			return [
				'x' => Carbon::parse($entry->time)->timestamp,
				'y' => $entry->five,
			];
		});

		return [$five];
	}
}
