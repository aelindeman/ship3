<?php

namespace App\Components;
use App\Behaviors\Graphable;
use App\Behaviors\Traits\DefaultGraphableAbsolute;
use App\Models\Component;

use DateInterval;

class LoadAverage extends Component implements Graphable
{
	protected $table = 'load';
	protected $fillable = [
		'one',
		'five',
		'fifteen'
	];

	protected $graphable = ['five'];

	public static function fetch()
	{
		return file_get_contents('/proc/loadavg');
	}

	public static function parse($input = null)
	{
		$p = preg_split ('/([\s]+|\/)/', $input);
		return [
			'one'     => (float)$p[0],
			'five'    => (float)$p[1],
			'fifteen' => (float)$p[2]
		];
	}

	use DefaultGraphableAbsolute;
}
