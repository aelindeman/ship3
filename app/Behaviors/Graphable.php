<?php

namespace App\Behaviors;

use DateInterval;

interface Graphable
{
	/**
	 * Returns data usable for Chartist graph series.
	 * @param $period int DateInterval to specify how far back to fetch data.
	 * @return array
	 */
	public function series(DateInterval $period = null);
}
