<?php

namespace App\Components;
use App\Behaviors\Graphable;
use App\Models\Component;

use DateInterval;

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

	public function series(DateInterval $period = null)
	{
		$since = $period ?
			app('carbon')->now()->sub($period) :
			app('carbon')->now()->subHours(config('app.graph-width'));

		$query = app('db')->table($this->table)
			->where('time', '>=', $since)
			->orderBy('time', 'asc');

		$data = collect($query->get());

		$five = $data->map(function($entry, $index) {
			return [
				'x' => app('carbon')->parse($entry->time)->timestamp,
				'y' => $entry->five,
			];
		});

		return [$five];
	}
}
