<?php

namespace App\Behaviors\Traits;

use DateInterval;

trait DefaultGraphableDerivative
{
	public function series(DateInterval $period = null)
	{
		$since = $period ?
			app('carbon')->now()->sub($period) :
			app('carbon')->now()
				->sub(new DateInterval(config('ship.period')));

		$query = app('db')->table($this->table)
			->where('time', '>=', $since)
			->orderBy('time', 'asc');

		$data = collect($query->get());
		$output = collect();

		$graphable = property_exists($this, 'graphable') ?
			$this->graphable :
			$this->fillable;

		foreach ($graphable as $key) {
			$series = $data->map(function($entry, $index) use ($data, $key) {
				if ($previous = $data->get($index - 1)) {
					$t = ($entry->$key - $previous->$key) / 60;
					return [
						'x' => app('carbon')->parse($entry->time)->timestamp,
						'y' => max($t, 0),
					];
				}
				return null;
			});

			$output->push($series);
		}

		return $output->toArray();
	}
}
