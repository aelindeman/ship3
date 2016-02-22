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
	 * Return the class name if directly printed.
	 */
	public function __toString()
	{
		return get_class($this);
	}

	/*
	 * Default constructor tries to run the component.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->run();
	}

	/*
	 * Returns the short name (class name without namespace) of the component.
	 */
	public function getShortName()
	{
		$segments = explode('\\', get_class($this));
		return array_pop($segments);
	}

	/**
	 * Fetch data, parse it, cache it, and return it.
	 * @param $filter mixed Return only specified columns. Can be an array of
	 *   keys, or can be `true` to output only valid database column names.
	 * @return array output
	 */
	public function run($filter = false)
	{
		if (!$this->output) {
			$this->output = static::parse(static::fetch());
		}

		$name = $this->getShortName();
		if (($order = config('components.'.$name.'.order', false)) !== false) {
			$this->output['order'] = $order;
		}

		// return only columns with database fields, if requested
		if ($filter === true and !empty($this->fillable)) {
			return array_intersect_key($this->output, array_flip($this->fillable));
		} else if ($filter and is_array($filter)) {
			return array_intersect_key($this->output, array_flip($filter));
		}

		return $this->output;
	}
}
