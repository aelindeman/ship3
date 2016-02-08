<?php

namespace App\Components;
use App\Models\Component;

class Uptime extends Component
{
	protected $table = false;

	public static function fetch()
	{
		return file_get_contents('/proc/uptime');
	}

	public static function parse($input = null)
	{
		list($up, $idle) = preg_split('/\s+/', $input, 2);
		return [
			'uptime' => (float)$up
		];
	}
}
