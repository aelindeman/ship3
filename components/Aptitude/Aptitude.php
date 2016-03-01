<?php

namespace App\Components;
use App\Behaviors\Cacheable;
use App\Helpers\ExecutableHelper;
use App\Models\Component;

use AdamBrett\ShellWrapper\Command\Builder;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\ExitCodes;

class Aptitude extends Component implements Cacheable
{
	const APTITUDE_CACHE_KEY = 'components.Aptitude.data';

	protected $table = false;

	public static function flush()
	{
		app('cache')->forget(static::APTITUDE_CACHE_KEY);
		return app('cache')->flush();
	}

	public static function fetch()
	{
		// use cache if it's there
		$expires = config('components.Aptitude.config.cache', 1440);
		if (app('cache')->has(static::APTITUDE_CACHE_KEY)) {
			return app('cache')->get(static::APTITUDE_CACHE_KEY);
		}

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
			$out = $shell->getOutput();
			app('cache')->put(static::APTITUDE_CACHE_KEY, $out, $expires);
			return $out;
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
				if (!config('components.Aptitude.config.packages', true)) {
					return [
						'count' => $count
					];
				}

				continue;
			}

			// get package list
			if (preg_match('/^Inst\s+(.*?)\s+\[(.*?)\]\s+\((.*?)\s+/i', $line, $matches)) {
				list(, $name, $fromVersion, $toVersion) = $matches;
				array_push($packages, [
					'name' => $name,
					'from' => $fromVersion,
					'to' => $toVersion
				]);
			}
		}

		return [
			'count' => $count,
			'packages' => $packages
		];
	}
}
