<?php

namespace App\Behaviors;

interface Graphable
{
	/**
	 * Returns data usable for Chartist graph series.
	 * @param $period int DateInterval to specify how far back to fetch data.
	 * @param $limit int Effectively splices the returned data by limiting only
	 *   $limit many records.
	 * @return array
	 */
	public function series(\DateInterval $period = null, $limit = false);
}
