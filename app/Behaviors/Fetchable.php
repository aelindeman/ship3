<?php

namespace App\Behaviors;

interface Fetchable
{
	/*
	 * Fetches raw data from whatever source, to be passed to parse().
	 */
	public static function fetch();
}
