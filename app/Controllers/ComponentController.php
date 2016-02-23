<?php

namespace App\Controllers;
use App\Behaviors\Cacheable;
use App\Behaviors\Graphable;
use App\Exceptions\ComponentNotFoundException;
use App\Models\Component;

use Illuminate\Filesystem\Filesystem;
use Laravel\Lumen\Routing\Controller;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ComponentController extends Controller
{
	/*
	 * Path to components directory
	 */
	protected $componentsPath;

	/*
	 * Component root namespace
	 */
	protected $componentNamespace = 'App\\Components\\';

	/*
	 * List of available components
	 */
	protected $components;

	/*
	 * List of registered components
	 */
	protected $registered;

	/*
	 * Default constructor
	 */
	public function __construct()
	{
		$this->componentsPath = base_path(
			config('app.component-path', 'components')
		);
		$this->components = collect();
		$this->registered = collect();
	}

	/**
	 * Activate all enabled components, or one specific component.
	 * @param $component string Component class name
	 * @param $force boolean Force re-registering and reloading components
	 * @return ComponentController
	 */
	public function run($component = null, $force = false)
	{
		if ($component and !is_array($component)) {

			if (!$force and $this->components->has($component)) {
				return $this;
			}

			// initialize it
			$class = $this->componentNamespace.$component;
			$path = $this->componentsPath.'/'.$component;

			$this->registerComponent($class, $path);
			$this->loadComponent($class, $path);

		} else {

			// return components if already initialized
			if (!$force and (
				$component and !$this->components->intersect($component)->isEmpty()
			) or (
				!$this->components->isEmpty()
			)) {
				return $this;
			}

			$components = $component ?: $this->listComponents();

			// initialize all components
			foreach ($components as $path => $class) {
				$this->registerComponent($class, $path);
				$this->loadComponent($class, $path);
			}

		}

		return $this;
	}

	/*
	 * Flushes component caches and returns a new ComponentController instane.
	 */
	public function flush()
	{
		if ($this->components->isEmpty() or $this->registered->isEmpty()) {
			throw new \RuntimeException('No components registed');
		}

		$this->components->filter(function($component) {
			return $component instanceOf Cacheable;
		})->each(function($component) {
			$component::flush();
		});

		// reset components to force data reload
		$this->components = collect();
		$this->registered = collect();

		return $this;
	}

	/**
	 * Returns a collection of data usable by Chartist
	 * @param $period int CarbonInterval-compatible time period, relative
	 *   to the current time, to specify how far back to fetch data.
	 * @param $limit int Effectively splices the returned data by limiting only
	 *   $limit many records.
	 * @return Collection
	 */
	public function getGraphData($period = null, $limit = null)
	{
		if ($this->components->isEmpty() or $this->registered->isEmpty()) {
			throw new \RuntimeException('No components registed');
		}

		$data = $this->components->filter(function($component) {
			return $component instanceOf Graphable;
		})->map(function($component) use ($period, $limit) {
			$component->run();
			return $component->series($period, $limit);
		});

		if ($data->isEmpty()) {
			throw new \DomainException('Component did not provide graph data');
		}

		return $data;
	}

	/**
	 * Returns a collection of the output of all active components.
	 * @return Collection output
	 */
	public function getData()
	{
		if ($this->components->isEmpty() or $this->registered->isEmpty()) {
			throw new \RuntimeException('No components registed');
		}

		// get data for each component
		$data = $this->components->map(function($component) {
			try {
				return $component->run();
			} catch (\RuntimeException $e) {
				// component works, but did not run successfully
				app('log')->debug('Caught '.get_class($e).' in '.get_class($component).': '.$e->getMessage());
			} catch (\LogicException $e) {
				// component was not configured correctly
				app('log')->notice('Caught '.get_class($e).' in '.get_class($component).': '.$e->getMessage());
			} catch (\Exception $e) {
				// anything else
				app('log')->notice('Caught '.get_class($e).' in '.get_class($component).': '.$e->getMessage());
			}
			return null;
		});

		return $data;
	}

	/**
	 * Returns a collection of all active component objects.
	 * @return Collection component objects
	 */
	public function getComponents()
	{
		if ($this->components->isEmpty() or $this->registered->isEmpty()) {
			throw new \RuntimeException('No components registered');
		}

		return $this->components;
	}

	/**
	 * Registers components but does not activate them.
	 * @return Collection names of components registered
	 */
	public function registerComponents()
	{
		if (!$this->registered->isEmpty()) {
			return $this->registered;
		}

		foreach ($this->listComponents() as $path => $class) {
			$this->registerComponent($class, $path);
		}

		return $this->registered;
	}

	/**
	 * Lists all installed components.
	 * @return Collection Collection of installed components.
	 */
	public function listComponents()
	{
		$components = collect();

		if (!app('files')->isDirectory($this->componentsPath)) {
			throw new \InvalidArgumentException('Component path is not a directory.');
		}

		// create iterator in components directory
		$iter = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$this->componentsPath,
				RecursiveDirectoryIterator::FOLLOW_SYMLINKS
			)
		);
		$iter->setMaxDepth(1);
		$iter->rewind();

		// iterate through each component folder
		while ($iter->valid()) {
			if ($iter->isFile()) {
				$path = dirname($iter->getPathname());
				if ($iter->getFilename() == basename($path).'.php') {
					$namespace = $this->componentNamespace.basename($path);
					$components->put($path, $namespace);
				}
			}
			$iter->next();
		}

		return $components;
	}

	/*
	 * Includes and instantiates a component, if it is enabled.
	 */
	protected function loadComponent($class, $path)
	{
		// check that component path exists
		if (!app('files')->isDirectory($path)) {
			throw new ComponentNotFoundException('Component not found');
		}

		// load config to check if component should be activated
		// assume yes if config doesn't exist
		$config = self::getComponentConfiguration($path);
		if ($config) {
			if (!$config['enabled']) {
				app('log')->debug($class.' is disabled');
				return null;
			}
		} else {
			app('log')->debug($class.' activated inferably (no config)');
		}

		$classPath = $path.'/'.basename($path).'.php';

		// include if autoloader failed
		if (!class_exists($class)) {
			include_once $classPath;
		}

		// if it still doesn't exist, that probably means it's broken
		if (!class_exists($class)) {
			app('log')->info('Class for '.$class.' not found (broken?)');
			return null;
		}

		// instantiate the class
		$name = self::getComponentName($class);
		return $this->components->put($name, new $class);
	}

	/*
	 * Registers a component's resources with the app.
	 */
	protected function registerComponent($class, $path)
	{
		$name = self::getComponentName($class);

		// check that component path exists
		if (!app('files')->isDirectory($path)) {
			throw new ComponentNotFoundException('Component not found');
		}

		// register configuration
		if ($config = self::getComponentConfiguration($path)) {
			foreach ($config as $key => $value) {
				app('config')->set("components.${name}.${key}", $value);
			}
		}

		// register translations
		$langPath = $path.'/lang';
		if (app('files')->isDirectory($langPath)) {
			app('translator')->addNamespace($name, $langPath);
		}

		// register views
		$viewsPath = $path.'/views';
		if (app('files')->isDirectory($viewsPath)) {
			app('view')->addNamespace($name, $viewsPath);
		}

		return $this->registered->put($class, $path);
	}

	/**
	 * Loads, but *does not register*, a component's configuration.
	 * @return array|boolean Component configuration, or false if not found
	 */
	public static function getComponentConfiguration($path)
	{
		$file = $path.'/config.php';
		return app('files')->exists($file) ?
			include($file) :
			false;
	}

	/**
	 * Translates a component namespace into its root class name.
	 * @return string Class name
	 */
	public static function getComponentName($component)
	{
		if (is_object($component)) {
			$component = get_class($component);
		}

		$pieces = explode('\\', $component);
		return array_pop($pieces);
	}
}
