<?php

namespace App\Components;
use App\Behaviors\Differentiable;
use App\Behaviors\Graphable;
use App\Models\Component;

use Carbon\Carbon;
use DateInterval;
use DateTime;

class NetTraffic extends Component
	implements Differentiable, Graphable
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
			app('carbon')->now()->subHours(config('ship.graph-width'));

		$query = app('db')->table($this->table)
			->where('time', '>=', $since)
			->orderBy('time', 'asc');

		$data = collect($query->get());

		$tx = $data->map(function($entry, $index) use ($data) {
			if ($previous = $data->get($index - 1)) {
				$t = ($entry->tx - $previous->tx) / 60;
				return [
					'x' => app('carbon')->parse($entry->time)->timestamp,
					'y' => max($t, 0),
				];
			}
			return null;
		});

		$rx = $data->map(function($entry, $index) use ($data) {
			if ($previous = $data->get($index - 1)) {
				$r = ($entry->rx - $previous->rx) / 60;
				return [
					'x' => app('carbon')->parse($entry->time)->timestamp,
					'y' => max($r, 0),
				];
			}
			return null;
		});

		return [$tx, $rx];
	}

	public function difference(DateInterval $period, DateTime $from = null)
	{
		if (app('db')->table($this->table)->count() < 2) {
			throw new \RangeException('Not enough database entries to be able to run a comparison');
		}

		$start = $from ?
			app('carbon')->parse($from) :
			app('carbon')->now();

		$end = $period ?
			$from->sub($period) :
			$from->subHours(config('ship.graph-width'));

		$a = app('db')->table($this->table)
			->select($this->fillable)
			->where('time', '<=', $start)
			->orderBy('time', 'desc')
			->first();

		$b = app('db')->table($this->table)
			->select($this->fillable)
			->where('time', '<=', $end)
			->orderBy('time', 'desc')
			->first();

		$values = array_map(function($key) use ($a, $b) {
			return $a->$key - $b->$key;
		}, $this->fillable);

		return array_combine($this->fillable, $values);
	}
}
