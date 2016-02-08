<?php

namespace App\Components;
use App\Helpers\ExecutableHelper;
use App\Models\Component;

use AdamBrett\ShellWrapper\Command\Builder;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\ExitCodes;

class Aptitude extends Component
{
	/*
	 * Component does not save data to the database
	 */
	protected $table = false;

	public static function fetch()
	{
		// use specified path to apcaccess, or caluclate it from path
		$bin = ExecutableHelper::getExecutablePath(
			config('components.Aptitude.executable', 'apt-get')
		);

		$shell = new Exec();
		$command = new Builder($bin);

		$command->addSubCommand('upgrade')
			->addFlag('u')
			->addFlag('s');

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
		$count = null; $packages = [];
		foreach ($input as $line) {
			// get update count
			if ($count === null and
				preg_match('/^([0-9]+) upg/', $line, $matches)) {
				$count = (int)$matches[1];
				// return if all we wanted was package count
				if (!config('Aptitude::config.get-packages', true)) {
					return [ 'count' => $count ];
				}
				continue;
			}

			// get package list
			if (preg_match('/^Inst\s+(.*?)\s+\[(.*?)\]\s+\((.*?)\s+/i', $line, $matches)) {
				list(, $name, $fromVersion, $toVersion) = $matches;
				$packages[$name] = [$fromVersion, $toVersion];
			}
		}

		return [
			'count' => $count,
			'packages' => $packages
		];
	}
}
