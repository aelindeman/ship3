<?php

namespace App\Controllers;
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
	 * Activate all enabled components.
	 * @return ComponentController
	 */
	public function run()
	{
		foreach ($this->listComponents() as $path => $class) {
			try {
				$this->registerComponent($class, $path);
				$this->loadComponent($class, $path);
			} catch (\Exception $e) {
				$name = self::getComponentName($class);
				app('log')->notice('Failed to run '.$name.': '.$e->getMessage());
			}
		}
		return $this;
	}

	/**
	 * Activate a single component by name.
	 * @return ComponentController
	 */
	public function runOne($name)
	{
		// if it's already stored, just return it
		if ($this->components->has($name)) {
			return $this->components->pull($name);
		}

		// initialize it
		$class = $this->componentNamespace.$name;
		$path = $this->componentsPath.'/'.$name;

		try {
			$this->registerComponent($class, $path);
			$this->loadComponent($class, $path);
		} catch (\Exception $e) {
			app('log')->notice('Failed to run '.$name.': '.$e->getMessage());
		}

		return $this;
	}

	/**
	 * Returns a collection of the output of all active components.
	 * @return Collection output
	 */
	public function getData()
	{
		if ($this->components->isEmpty() or $this->registered->isEmpty()) {
			throw new \RuntimeException('No components registed.');
		}

		return $this->components->map(function ($component) {
			return $component->run();
		});
	}

	/**
	 * Returns a collection of all active component objects.
	 * @return Collection component objects
	 */
	public function getComponents()
	{
		if ($this->components->isEmpty() or $this->registered->isEmpty()) {
			throw new \RuntimeException('No components registered.');
		}

		return $this->components;
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
			throw new \RuntimeException($class.' does not exist.');
		}

		// load config to check if component should be activated
		// assume yes if config doesn't exist
		$config = self::getComponentConfiguration($path);
		if ($config) {
			if (!$config['enabled']) {
				app('log')->debug($class.' not activated (disabled)');
				return;
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
			app('log')->info($class.' not activated (class name?)');
			return;
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
		// check that component path exists
		if (!app('files')->isDirectory($path)) {
			throw new \RuntimeException($class.' does not exist.');
		}

		$name = self::getComponentName($class);

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
