<?php

namespace App\Behaviors;

use DateInterval;
use DateTime;

interface Differentiable
{
	/**
	 * Provides a difference between the two records.
	 * @param $period DateInterval How far back to fetch data.
	 * @param $from DateTime A time to use for the record to compare to.
	 *   Defaults to a new reading if null.
	 * @return array
	 */
	public function difference(DateInterval $period, DateTime $from = null);
}
