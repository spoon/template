<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace spoon\template;
use spoon\template\extension\Core;

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
	 * An array of extension instances.
	 *
	 * @var array
	 */
	protected $extensions;

	/**
	 * Class constructor.
	 *
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

		// load default extensions
		$this->extensions = array(
			'core' => new Core(),
		);
	}

	/**
	 * Add an extension.
	 *
	 * @return Template
	 * @param Extension $extension
	 */
	public function addExtension(Extension $extension)
	{
		$this->extensions[$extension->getName()] = $extension;
		return $this;
	}

	/**
	 * Disable auto escaping of variables.
	 *
	 * @return Template
	 */
	public function disableAutoEscape()
	{
		$this->autoEscape = false;
		return $this;
	}

	/**
	 * Disable automatically reloading templates based on the modification date.
	 *
	 * @return Template
	 */
	public function disableAutoReload()
	{
		$this->autoReload = false;
		return $this;
	}

	/**
	 * Disable debug mode.
	 *
	 * @return Template
	 */
	public function disableDebug()
	{
		$this->debug = false;
		return $this;
	}

	/**
	 * Enable auto escaping of variables.
	 *
	 * @return Template
	 */
	public function enableAutoEscape()
	{
		$this->autoEscape = true;
		return $this;
	}

	/**
	 * Enable auto reload.
	 *
	 * @return Template
	 */
	public function enableAutoReload()
	{
		$this->autoReload = true;
		return $this;
	}

	/**
	 * Enable debug mode.
	 *
	 * @return Template
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
	 * @return string
	 * @param string $filename The filename (including the path) you want to know the filename for.
	 */
	public function getCacheFilename($filename)
	{
		return md5(realpath($filename)) . '_' . basename($filename) . '.php';
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
	 * Get a specific extension instance.
	 *
	 * @return Extension
	 * @param string $name The name of the extension.
	 */
	public function getExtension($name)
	{
		if(!isset($this->extensions[$name]))
		{
			throw new Exception(sprintf('The "%s" extension is not enabled.', (string) $name));
		}

		return $this->extensions[$name];
	}

	/**
	 * Get all the extensions.
	 *
	 * @return array
	 */
	public function getExtensions()
	{
		return $this->extensions;
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
	 * @return bool
	 * @param string $template The location of the cached template file.
	 * @param int $time The timestamp to compare with.
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
	 * Remove an extension from the list.
	 *
	 * @return Template
	 * @param string $name The name of the extension.
	 */
	public function removeExtension($name)
	{
		unset($this->extensions[$name]);
		return $this;
	}

	/**
	 * Set the template caching directory.
	 *
	 * @return Template
	 * @param string $cache The location where the cached templates should be stored.
	 */
	public function setCache($cache)
	{
		$this->cache = (string) $cache;
		return $this;
	}

	/**
	 * Set the charset.
	 *
	 * @return Template
	 * @param string $charset The default charset to use.
	 */
	public function setCharset($charset)
	{
		$this->charset = (string) $charset;
		return $this;
	}

	/**
	 * Set multiple extensions at once.
	 *
	 * @return Template
	 * @param array $extensions An array of extension instances to add.
	 */
	public function setExtensions(array $extensions)
	{
		foreach($extensions as $extension)
		{
			$this->addExtension($extension);
		}

		return $this;
	}
}
