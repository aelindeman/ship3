<?php

namespace App\Components;
use App\Helpers\ExecutableHelper;
use App\Models\Component;

use AdamBrett\ShellWrapper\Command\Builder;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\ExitCodes;

class UPS extends Component
{
	protected $table = 'ups';
	protected $fillable = [
		'status',
		'charge',
		'load',
		'battvoltage',
		'linevoltage'
	];

	public static function fetch()
	{
		// use specified path to apcaccess, or caluclate it from path
		$bin = ExecutableHelper::getExecutablePath(
			config('components.UPS.executable', 'apcaccess')
		);

		$shell = new Exec();
		$command = new Builder($bin);

		// specify host, if it's in the config
		if ($host = config('components.UPS.host')) {
			$command->addFlag('h', $host);
		}

		$shell->run($command);

		// set data if the command ran successfully, or throw an exception
		if (($exit = $shell->getReturnValue()) == ExitCodes::SUCCESS) {
			return $shell->getOutput();
		} else {
			throw new \RuntimeException(ExitCodes::getDescription($exit));
		}
	}

	public static function parse($input = null)
	{
		$out = [];
		foreach ($input as $row) {
			if (preg_match('/^(.*?)\s*:\s*(.*?)$/', $row, $matches)) {
				list(, $key, $value) = $matches;
				$key = preg_replace('/\s+/', '-', strtolower($key));
				$out[$key] = $value;
			}
		}
		return $out;
	}
}
