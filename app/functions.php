<?php

if (!function_exists('bytesize')) {

	/**
	 * Formats a given size (in kilobytes) and returns a string with the
	 *   appropriate suffix.
	 *
	 * @param $input int Size to format, in kilobytes
	 * @param $precision int Number of decimal places
	 * @param $space string Separator between value and byte suffix
	 * @return string Formatted size with suffix
	 */
	function bytesize($input, $precision = null, $space = '')
	{
		$suffix = ['Y', 'Z', 'E', 'P', 'T', 'G', 'M', 'k'];
		$total = count($suffix);

		while ($total -- and $input > 1024) {
			$input /= 1024;
		}

		$decimals = $precision ?
			$precision :
			($input < 10 ? 2 : ($input < 100 ? 1 : 0));

		return round($input, $decimals).e($space).$suffix[$total];
	}

}
