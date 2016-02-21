<?php

namespace App\Behaviors;

interface Graphable
{
	/*
	 * Returns data usable for Chartist graph series.
	 */
	public function series();
}
