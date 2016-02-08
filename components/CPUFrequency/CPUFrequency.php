<?php

namespace App\Components;
use App\Models\Component;

class CPUFrequency extends Component
{
	protected $table = 'cpufreq';
	protected $fillable = [
		'average'
	];

	public static function fetch()
	{
		return file_get_contents('/proc/cpuinfo');
	}

	public static function parse($input = null)
	{
		// field names and translations
		$fields = [
			// 'model name' => 'model',
			'processor' => 'id',
			'cpu MHz' => 'freq'
		];

		$cpus = []; $current = [];
		foreach (explode(PHP_EOL, $input) as $i => $line) {

			// section ends after a blank newline
			if (preg_match('/^\s*$/', $line)) {
				if (count($current)) {
					$cpus[] = $current;
					$current = [];
					continue;
				}
			}

			// get field
			if (preg_match('/^(.*?)\s*:\s*(.*?)$/i', $line, $matches)) {
				list(, $key, $value) = $matches;
				if (in_array($key, array_keys($fields))) {
					$key = strtr($key, $fields);
					$current[$key] = $value;
				}
			}

		}

		// merge into one array
		foreach ($cpus as $i => $c) {
			$cpus[$c['id']] = (float)$c['freq'];
		}

		// calculate average cpu core frequency
		$average = array_sum($cpus) / (count($cpus) ?: 1);

		return [
			'average' => $average,
			'per_cpu' => $cpus
		];
	}
}
