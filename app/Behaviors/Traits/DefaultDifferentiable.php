<?php

namespace App\Behaviors\Traits;

use DateInterval;
use DateTime;

trait DefaultDifferentiable
{
	public function difference(DateInterval $period, DateTime $from = null)
	{
		$start = $from ?
			app('carbon')->parse($from) :
			app('carbon')->now();

		$end = $start->copy()->sub($period);

		$a = app('db')->table($this->table)
			->where('time', '<=', $start)
			->orderBy('time', 'desc')
			->first();

		$b = app('db')->table($this->table)
			->where('time', '<', $end)
			->orderBy('time', 'desc')
			->first();

		$values = array_map(function($key) use ($a, $b) {
			return $b ?
				$a->$key - $b->$key :
				$a->$key;
		}, $this->fillable);

		return array_combine($this->fillable, $values);
	}
}
