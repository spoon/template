<?php

namespace spoon\template;

class Autoloader
{
	/**
	 * Attempt to load this class.
	 *
	 * @param string $class The full name of the class (including namespace) to be loaded.
	 */
	public static function autoload($class)
	{
		if(stripos(($class = strtolower($class)), __NAMESPACE__) === false)
		{
			return '';
		}

		if(file_exists($file = dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace(__NAMESPACE__ . '\\', '', $class) . '.php'))
		{
			require $file;
		}
	}

	/**
	 * Register this autoloader.
	 */
	public static function register()
	{
		spl_autoload_register(array(new self, 'autoload'));
	}
}
