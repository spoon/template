<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace Spoon\Template;

/**
 * Used to hold all the environment specific options for templates.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Environment
{
	/**
	 * If enabled, variables will be automatically escaped.
	 *
	 * @var bool
	 */
	protected $autoEscape;

	/**
	 * If enabled, cached templates will be automatically reloaded if the template changes.
	 *
	 * @var bool
	 */
	protected $autoReload;

	/**
	 * Location where the cached templates will be stored.
	 *
	 * @var string
	 */
	protected $cache;

	/**
	 * Charset that will be used internally.
	 *
	 * @var string
	 */
	protected $charset;

	/**
	 * If enabled, debug info will be available in the templates.
	 *
	 * @var bool
	 */
	protected $debug;

	/**
	 * List of all modifiers that apply in this environment.
	 *
	 * @var array
	 */
	protected $modifiers;

	/**
	 * @param array[optional] $options An array containing options.
	 */
	public function __construct(array $options = array())
	{
		// default configuration
		$options = array_merge(array(
			'auto_escape' => true,
			'auto_reload' => true,
			'cache' => '.',
			'charset' => 'utf-8',
			'debug' => false,
		), $options);

		// load configuration
		$this->autoEscape = (bool) $options['auto_escape'];
		$this->autoReload = (bool) $options['auto_reload'];
		$this->cache = (string) $options['cache'];
		$this->charset = (string) $options['charset'];
		$this->debug = (bool) $options['debug'];

		// load default modifiers
		// @todo load actual default modifiers and not this linked crap
		$this->modifiers = array(
			'dump' => array('spoon\debug\Debug', 'dump')
		);
	}

	/**
	 * Add a modifier with a callback.
	 *
	 * @param string $name
	 * @param mixed $value The value should be a valid callback (see http://php.net/manual/en/language.pseudo-types.php)
	 * @return Spoon\Template\Template
	 */
	public function addModifier($name, $value)
	{
		if(!preg_match('/^[a-z]+[a-z0-9_]*$/i', $name))
		{
			throw new Exception(sprintf('Modifier "%s" is not following the naming conventions', $name));
		}

		$this->modifiers[(string) $name] = $value;
		return $this;
	}

	/**
	 * Disable auto escaping of variables.
	 *
	 * @return Spoon\Template\Template
	 */
	public function disableAutoEscape()
	{
		$this->autoEscape = false;
		return $this;
	}

	/**
	 * Disable automatically reloading templates based on the modification date.
	 *
	 * @return Spoon\Template\Template
	 */
	public function disableAutoReload()
	{
		$this->autoReload = false;
		return $this;
	}

	/**
	 * Disable debug mode.
	 *
	 * @return Spoon\Template\Template
	 */
	public function disableDebug()
	{
		$this->debug = false;
		return $this;
	}

	/**
	 * Enable auto escaping of variables.
	 *
	 * @return Spoon\Template\Template
	 */
	public function enableAutoEscape()
	{
		$this->autoEscape = true;
		return $this;
	}

	/**
	 * Enable auto reload.
	 *
	 * @return Spoon\Template\Template
	 */
	public function enableAutoReload()
	{
		$this->autoReload = true;
		return $this;
	}

	/**
	 * Enable debug mode.
	 *
	 * @return Spoon\Template\Template
	 */
	public function enableDebug()
	{
		$this->debug = true;
		return $this;
	}

	/**
	 * Get cache location.
	 *
	 * @return string
	 */
	public function getCache()
	{
		return $this->cache;
	}

	/**
	 * Fetch the cache filename.
	 *
	 * @param string $filename The filename (including the path) you want to know the filename for.
	 * @return string
	 */
	public function getCacheFilename($filename)
	{
		return 'S' . md5(realpath($filename)) . '_' . basename($filename) . '.php';
	}

	/**
	 * Get charset.
	 *
	 * @return string
	 */
	public function getCharset()
	{
		return $this->charset;
	}

	/**
	 * Get a specific modifier callback.
	 *
	 * @param string $name The name of the extension.
	 * @return mixed
	 */
	public function getModifier($name)
	{
		if(!isset($this->modifiers[$name]))
		{
			throw new Exception(sprintf('There is no "%s" modifier.', (string) $name));
		}

		return $this->modifiers[$name];
	}

	/**
	 * Get all the modifiers.
	 *
	 * @return array
	 */
	public function getModifiers()
	{
		return $this->modifiers;
	}

	/**
	 * Is auto escaping enabled.
	 *
	 * @return bool
	 */
	public function isAutoEscape()
	{
		return $this->autoEscape;
	}

	/**
	 * Is auto reload enabled.
	 *
	 * @return bool
	 */
	public function isAutoReload()
	{
		return $this->autoReload;
	}

	/**
	 * Is the cached template modified.
	 *
	 * @param string $template The location of the cached template file.
	 * @param int $time The timestamp to compare with.
	 * @return bool
	 */
	public function isChanged($template, $time)
	{
		return (filemtime((string) $template) > (int) $time);
	}

	/**
	 * Is debug mode enabled.
	 *
	 * @return bool
	 */
	public function isDebug()
	{
		return $this->debug;
	}

	/**
	 * Remove a modifier from the list.
	 *
	 * @param string $name
	 * @return Spoon\Template\Template
	 */
	public function removeModifier($name)
	{
		unset($this->modifiers[$name]);
		return $this;
	}

	/**
	 * Set the template caching directory.
	 *
	 * @param string $cache The location where the cached templates should be stored.
	 * @return Spoon\Template\Template
	 */
	public function setCache($cache)
	{
		$this->cache = (string) $cache;
		return $this;
	}

	/**
	 * Set the charset.
	 *
	 * @param string $charset The default charset to use.
	 * @return Spoon\Template\Template
	 */
	public function setCharset($charset)
	{
		$this->charset = (string) $charset;
		return $this;
	}
}
