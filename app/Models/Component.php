<?php

namespace App\Models;
use App\Behaviors\Fetchable;
use App\Behaviors\Parseable;

use Illuminate\Database\Eloquent\Model;

abstract class Component extends Model implements Fetchable, Parseable
{
	/*
	 * Don't use database table by default. Requires table names to be
	 *   manually specified if component stores data.
	 */
	protected $table = false;

	/*
	 * Don't include created_at or updated_at timestamps, we'll use our own.
	 */
	public $timestamps = false;

	/*
	 * Cache output.
	 */
	protected $output;

	/*
	 * Default constructor tries to run the component, and writes to the log if
	 *   something went wrong.
	 */
	public function __construct()
	{
		parent::__construct();

		try {
			$this->run();
		} catch (\RuntimeException $e) {
			// component works, but did not run successfully
			app('log')->debug('Caught '.get_class($e).' in '.get_class($this).': '.$e->getMessage());
		} catch (\LogicException $e) {
			// component was not configured correctly
			app('log')->notice('Caught '.get_class($e).' in '.get_class($this).': '.$e->getMessage());
		} catch (\Exception $e) {
			// anything else
			app('log')->notice('Caught '.get_class($e).' in '.get_class($this).': '.$e->getMessage());
		}
	}

	/*
	 * Fetch data, parse it, cache it, and return it.
	 */
	public function run()
	{
		if (!$this->output) {
			$this->output = static::parse(static::fetch());
		}
		return $this->output;
	}

	/*
	 * Just fetch the output, don't run anything.
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/*
	 * Return the class name if directly printed.
	 */
	public function __toString()
	{
		return get_class($this);
	}
}
