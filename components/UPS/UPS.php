<?php

namespace App\Components;
use App\Behaviors\Graphable;
use App\Behaviors\Traits\DefaultGraphableAbsolute;
use App\Helpers\ExecutableHelper;
use App\Models\Component;

use AdamBrett\ShellWrapper\Command\Builder;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\ExitCodes;

class UPS extends Component implements Graphable
{
	protected $table = 'ups';
	protected $fillable = [
		'status',
		'bcharge',
		'loadpct',
		'battv',
		'linev'
	];

	protected $graphable = [
		'loadpct',
		'bcharge',
		'linev',
		'battv'
	];

	public static function fetch()
	{
		$command = config('components.UPS.executable', 'apcaccess');
		$bin = ExecutableHelper::getExecutablePath($command,
			ExecutableHelper::STRATEGY_ALL | ExecutableHelper::STRATEGY_POSIX_USUAL_PATHS
		);

		if (!$bin) {
			throw new \RuntimeException('Command not found ('.$command.')');
		}

		$shell = new Exec();
		$command = new Builder($bin);
		$command->addFlag('u'); // strip unit labels

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
				$key = preg_replace('/\s+/', '_', strtolower($key));

				// transform a couple values
				switch ($key) {
					case 'status':
						$value = strtolower($value);
						break;
					case 'xonbatt':
						$value = $value ?
							app('carbon')->parse($value)->diffForHumans() :
							app('translator')->get('UPS::component.labels.no-lastxfer');
						break;
				}

				$out[$key] = $value;
			}
		}
		return $out;
	}

	use DefaultGraphableAbsolute;
}
