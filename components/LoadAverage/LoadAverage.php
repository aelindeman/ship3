<?php

namespace App\Components;
use App\Models\Component;

class LoadAverage extends Component
{
	protected $table = 'load';
	protected $fillable = [
		'one',
		'five',
		'fifteen'
	];

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
}
