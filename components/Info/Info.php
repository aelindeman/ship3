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
		];
	}

	public static function parse($input = null)
	{
		return $input;
	}
}
