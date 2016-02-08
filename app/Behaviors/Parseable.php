<?php

namespace App\Behaviors;

interface Parseable
{
	/*
	 * Parses raw data, to be output to the database or page.
	 */
	public static function parse($input = null);
}
