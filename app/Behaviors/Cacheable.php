<?php

namespace App\Behaviors;

interface Cacheable
{
	/*
	 * Flushes cached data.
	 */
	public static function flush();
}
