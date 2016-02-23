<?php

namespace App\Components;
use App\Models\Component;

class Info extends Component
{
	protected $table = false;

	public static function fetch()
	{
		return [
			'hostname' => gethostname(),
			'kernel' => php_uname('s'),
			'release' => php_uname('r'),
			'arch' => php_uname('m'),
			'uptime' => file_get_contents('/proc/uptime')
		];
	}

	public static function parse($input = null)
	{
		list($up, $idle) = preg_split('/\s+/', $input['uptime'], 2);

		// partial times
		$secs = str_pad((int)($up % 60), 2, '0', STR_PAD_LEFT);
		$mins = str_pad((int)($up / 60 % 60), 2, '0', STR_PAD_LEFT);
		$hours = (int)($up / 3600 % 24);
		$days = (int)($up / 86400);

		// total times
		$totalMins = (int)($up / 60);
		$totalHours = round($up / 3600, 1);
		$totalDays = round($up / 86400, 2);

		// formatter dictionary
		$dict = [
			'@s' => $secs,
			'@m' => $mins,
			'@h' => $hours,
			'@d' => $days,
			'@M' => $totalMins,
			'@H' => $totalHours,
			'@D' => $totalDays,
			'_m' => substr(app('translator')->choice('ship.time.minute', $mins), 0, 1),
			'_h' => substr(app('translator')->choice('ship.time.hour', $hours), 0, 1),
			'_d' => substr(app('translator')->choice('ship.time.day', $days), 0, 1),
			'_M' => substr(app('translator')->choice('ship.time.minute', $totalMins), 0, 1),
			'_H' => substr(app('translator')->choice('ship.time.hour', $totalHours), 0, 1),
			'_D' => substr(app('translator')->choice('ship.time.day', $totalDays), 0, 1)
		];

		$format = config('components.Info.uptime.format', '@d_d @h:@m:@s');
		$formatted = strtr($format, $dict);

		return array_merge($input, [
			'uptime' => [
				'formatted' => $formatted,
				'raw' => (float)$up
			]
		]);
	}
}
