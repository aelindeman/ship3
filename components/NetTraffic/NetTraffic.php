<?php

namespace App\Components;
use App\Behaviors\Differentiable;
use App\Behaviors\Graphable;
use App\Behaviors\Traits\DefaultGraphableDerivative;
use App\Behaviors\Traits\DefaultDifferentiable;
use App\Models\Component;

use DateInterval;
use DateTime;

class NetTraffic extends Component implements Differentiable, Graphable
{
	protected $table = 'nettraffic';
	protected $fillable = [
		'tx',
		'rx'
	];

	public static function fetch()
	{
		return file_get_contents('/proc/net/dev');
	}

	public static function parse($input = null)
	{
		$interfaces = config('components.NetTraffic.interfaces', null);
		if (!$interfaces) {
			throw new \LogicException('No interfaces set');
		}

		$interfaces = explode(',', $interfaces);

		// source: http://www.onlamp.com/pub/a/linux/2000/11/16/LinuxAdmin.html
		$headers = ['rx_bytes', 'rx_packets', 'rx_errors', 'rx_drop',
			'rx_fifo', 'rx_frame', 'rx_compressed', 'rx_multicast',
			'tx_bytes', 'tx_packets', 'tx_errors', 'tx_drop', 'tx_fifo',
			'tx_colls', 'tx_carrier', 'tx_compressed' ];
		$stats = [];

		// parse input
		foreach (explode(PHP_EOL, $input) as $l) {
			foreach ($interfaces as $i => $iface) {
				if (preg_match('/^\s*('.$iface.'):\s*(.*)?$/i', $l, $m)) {
					$stats[$iface] = array_combine($headers, preg_split('/(?<=(\w|\:))(\s+)/', $m[2]));
					unset($interfaces[$i]);
				}
			}
		}

		// calculate totals
		$rx = 0; $tx = 0;

		// double-check we've got info for the specified interfaces
		if (count($stats) > 0) {
			foreach ($stats as $i => $t) {
				$rx += $t['rx_bytes'] / 1024;
				$tx += $t['tx_bytes'] / 1024;
			}
		}

		return [
			'rx' => $rx,
			'tx' => $tx
		];
	}

	use DefaultDifferentiable;
	use DefaultGraphableDerivative;
}
