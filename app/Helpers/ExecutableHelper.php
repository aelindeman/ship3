<?php

namespace App\Helpers;

use AdamBrett\ShellWrapper\Command\Builder;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\ExitCodes;
use DirectoryIterator;

class ExecutableHelper
{
	/*
	 * Bitmasks for settings that define strategies for finding an executable.
	 */
	const STRATEGY_WHICH = 0b001;
	const STRATEGY_ITERATOR = 0b010;
	const STRATEGY_ALL = 0b011;

	/*
	 * Optional setting for using common POSIX paths, instead of the ones
	 *   in use by the system.
	 */
	const STRATEGY_POSIX_USUAL_PATHS = 0b100;

	/*
	 * List of paths where binaries are commonly installed.
	 */
	const POSIX_USUAL_PATHS = '/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin';

	/**
	 * Allows components which require running commands from a shell to locate
	 *   the real path to their binary from $PATH.
	 *
	 * @param $command Binary to search for.
	 * @param $settings Strategies for finding the binary. (Safely skippable
	 *   using `null`.)
	 * @param $paths Array of paths to search when using iterator. Will search
	 *   in POSIX default paths if unspecified.
	 * @return string|false Path to executable, or false if not found.
	 */
	public static function getExecutablePath($command, $settings = null, $paths = null)
	{
		if ($settings === null) $settings = self::STRATEGY_ALL;

		// get paths from parameter, our list of defaults, or system $PATH
		$paths = is_array($paths) ?
			$paths :
			($settings & self::STRATEGY_POSIX_USUAL_PATHS) ?
				explode(':', self::POSIX_USUAL_PATHS) :
				explode(PATH_SEPARATOR, getenv('PATH'));

		// use `which` to find the binary
		if ($settings & self::STRATEGY_WHICH) {

			// reset the path if specified
			if ($settings & self::STRATEGY_POSIX_USUAL_PATHS) {
				putenv('PATH='.implode(PATH_SEPARATOR, explode(':', self::POSIX_USUAL_PATHS)));
			}

			$shell = new Exec();
			$which = new Builder('which');

			$which->addSubcommand($command);
			$shell->run($which);

			if ($shell->getReturnValue() == ExitCodes::SUCCESS) {
				return implode('', $shell->getOutput());
			}
		}

		// use a directory iterator to find it manually from the default paths list
		if ($settings & self::STRATEGY_ITERATOR) {
			foreach ($paths as $path) {
				if (file_exists($path) and is_dir($path)) {
					foreach (new DirectoryIterator($path) as $file) {
						if ($file->isFile() and
							$file->isExecutable() and
							$file->getBasename() == $command) {
							return $file->getPathname();
						}
					}
				}
			}
		}

		return false;
	}
}
