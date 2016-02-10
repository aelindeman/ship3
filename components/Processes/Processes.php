<?php

namespace App\Components;
use App\Behaviors\Cacheable;
use App\Helpers\ExecutableHelper;
use App\Models\Component;

use AdamBrett\ShellWrapper\Command\Builder;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\ExitCodes;

class Processes extends Component
{
	protected $table = false;

	public static function fetch()
	{
		// use specified path to apcaccess, or caluclate it from path
		$bin = ExecutableHelper::getExecutablePath(
			config('components.Processes.executable', 'ps')
		);

		$shell = new Exec();
		$command = new Builder($bin);

		// ps implementations differ by platform, so
		switch (config('components.Processes.args', strtolower(php_uname('s')))) {
			case 'freebsd':
				$command->addFlag('a')
					->addFlag('c')
					->addFlag('o', 'pid,user,%cpu,%mem,comm,args');
				break;
			case 'linux':
			default:
				$command->addFlag('e')
					->addFlag('o', 'pid,uname,pcpu,pmem,comm,args');
				break;
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
		$columns = ['pid', 'user', 'cpu', 'memory', 'name', 'args'];
		$split = count($columns);

		$lines = collect();
		foreach ($input as $i => $line) {
			if ($i == 0) continue; // skip header

			$line = preg_split('/\s+/', $line, $split, PREG_SPLIT_NO_EMPTY);
			$line = array_combine($columns, $line);
			$lines->push($line);
		}

		$count = config('components.Processes.count', 3);

		// sort and splice
		$topCpu = $lines->sortBy('cpu', SORT_REGULAR, true)->splice(0, $count);
		$topMemory = $lines->sortBy('memory', SORT_REGULAR, true)->splice(0, $count);

		return [
			'cpu' => $topCpu,
			'memory' => $topMemory
		];
	}
}
