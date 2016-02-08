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
	 * Activates all enabled components.
	 * @param $reload boolean Forcibly reload and reregister all components.
	 * @return ComponentController
	 */
	public function run($reload = false)
	{
		foreach ($this->listComponents() as $path => $class) {
			$this->registerComponent($class, $path);
			$this->loadComponent($class, $path);
		}
		return $this;
	}

	/**
	 * Returns a collection of the output of all active components.
	 * @return Collection output
	 */
	public function getData()
	{
		if (!$this->components or !$this->registered) {
			throw new \RuntimeException('Components must be activated first');
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
		if (!$this->components or !$this->registered) {
			throw new \RuntimeException('Components must be activated first');
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
		// first load (but don't register) the configuration to make sure the
		// component is enabled
		$config = self::getComponentConfiguration($path);
		if (!$config['enabled']) {
			app('log')->debug($class.' not activated (disabled)');
			return;
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
		$name = self::getComponentName($class);

		// load configuration
		foreach (self::getComponentConfiguration($path) as $key => $value) {
			app('config')->set("components.${name}.${key}", $value);
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
	 * @return array Component configuration
	 */
	public static function getComponentConfiguration($path)
	{
		$file = $path.'/config.php';
		return app('files')->exists($file) ?
			include($file) :
			[];
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
