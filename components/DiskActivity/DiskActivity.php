<?php

namespace App\Components;
use App\Behaviors\Graphable;
use App\Models\Component;

use DateInterval;

class DiskActivity extends Component implements Graphable
{
	protected $table = 'disk';
	protected $fillable = [
		'read',
		'write'
	];

	public static function fetch()
	{
		return file_get_contents('/proc/diskstats');
	}

	public static function parse($input = null)
	{
		// get disks
		$disks = explode(',', config('components.DiskActivity.disks', null));
		if (!$disks) {
			throw new \LogicException(app('translator')->get('DiskActivity::component.errors.no-disks'));
		}

		// get block size
		$bs = config('components.DiskActivity.blocksize', null);
		if (!$bs) {
			throw new \LogicException(app('translator')->get('DiskActivity::component.errors.blocksize-value'));
		}

		if (strpos($bs, ',') > -1) {
			$bs = explode(',', $bs);
		}

		if (is_array($bs)) {
			if (count($bs) != count($disks)) {
				throw new \LogicException(app('translator')->get('DiskActivity::component.errors.blocksize-count'));
			}
		}

		// source: https://www.kernel.org/doc/Documentation/iostats.txt
		$columns = [
			'disk' => ['m', 'mm', 'dev', 'reads', 'rd_mrg', 'rd_sectors',
				'ms_reading', 'writes', 'wr_mrg', 'wr_sectors',
				'ms_writing', 'cur_ios', 'ms_doing_io', 'ms_weighted'],
			'partition' => ['m', 'mm', 'dev', 'reads', 'rd_sectors',
				'writes', 'wr_sectors']
		];
		$stats = [];

		// parse input
		foreach (explode(PHP_EOL, $input) as $l) {
			if (empty($l)) continue;
			$l = preg_split('/(?<=\w)\s+/', trim($l));

			if (count($l) == count($columns['disk'])) {
				$stats[] = array_combine($columns['disk'], $l);
			} else if (count($l) == count($columns['partition'])) {
				$stats[] = array_combine($columns['partition'], $l);
			} else continue;
		}

		// calculate totals
		$writes = 0; $reads = 0;
		if (count($stats) > 0) {
			foreach ($stats as $i => $s) {
				if (in_array($s['dev'], $disks)) {
					$blocksize = is_array($bs) ? array_pop($bs) : $bs;
					// stat = sectors * bytes per sector / bytes per kb
					$reads += ($s['rd_sectors'] * $blocksize) / 1024;
					$writes += ($s['wr_sectors'] * $blocksize) / 1024;
				} else {
					unset($stats[$i]);
				}
				$i ++;
			}
		}

		return [
			'read' => $reads,
			'write' => $writes
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

		$read = $data->map(function($entry, $index) use ($data) {
			if ($previous = $data->get($index - 1)) {
				$r = ($entry->read - $previous->read) / 60;
				return [
					'x' => app('carbon')->parse($entry->time)->timestamp,
					'y' => $r,
				];
			}
			return null;
		});

		$write = $data->map(function($entry, $index) use ($data) {
			if ($previous = $data->get($index - 1)) {
				$w = ($entry->write - $previous->write) / 60;
				return [
					'x' => app('carbon')->parse($entry->time)->timestamp,
					'y' => $w,
				];
			}
			return null;
		});

		return [$read, $write];
	}
}
