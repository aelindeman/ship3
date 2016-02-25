<?php

namespace App\Behaviors;

use DateInterval;

interface Graphable
{
	/**
	 * Returns data usable for Chartist graph series.
	 * @param $period DateInterval How far back to fetch data, relative to now.
	 * @return array
	 */
	public function series(DateInterval $period = null);
}
