<?php

if (!function_exists('bytesize')) {

	/**
	 * Formats a given size (in kilobytes) and returns a string with the
	 *   appropriate suffix.
	 *
	 * @param $input int Size to format, in kilobytes
	 * @param $precision int Number of decimal places
	 * @param $space string Separator between value and byte suffix
	 * @param $maxiumum string Maximum
	 * @return string Formatted size with suffix
	 */
	function bytesize($input, $precision = 0, $space = false)
	{
		$suffixes =  ['Y', 'Z', 'E', 'P', 'T', 'G', 'M', 'k'];
		$total = count($suffixes);
		while ($total-- and $input > 10000) {
			$input /= 1024;
		}
		return round($input, $precision).e($space).$suffixes[$total];
	}

}
