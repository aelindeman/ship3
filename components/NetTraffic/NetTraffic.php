<?php

namespace App\Components;
use App\Behaviors\Graphable;
use App\Models\Component;

use DateInterval;

class NetTraffic extends Component implements Graphable
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

	public function series(DateInterval $period = null)
	{
		$since = $period ?
			app('carbon')->now()->sub($period) :
			app('carbon')->now()->subHours(config('app.graph-width'));

		$query = app('db')->table($this->table)
			->where('time', '>=', $since)
			->orderBy('time', 'asc');

		$data = collect($query->get());

		$tx = $data->map(function($entry, $index) use ($data) {
			if ($previous = $data->get($index - 1)) {
				$t = ($entry->tx - $previous->tx) / 60;
				return [
					'x' => app('carbon')->parse($entry->time)->timestamp,
					'y' => $t,
				];
			}
			return null;
		});

		$rx = $data->map(function($entry, $index) use ($data) {
			if ($previous = $data->get($index - 1)) {
				$r = ($entry->rx - $previous->rx) / 60;
				return [
					'x' => app('carbon')->parse($entry->time)->timestamp,
					'y' => $r,
				];
			}
			return null;
		});

		return [$tx, $rx];
	}
}
