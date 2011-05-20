<?php

namespace spoon\template;

class Component
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
	 * If enabled, the template will output error messages for flawed syntax.
	 *
	 * @var bool
	 */
	protected $strict;

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
			'strict' => false,
		), $options);

		// load configuration
		$this->autoEscape = (bool) $options['auto_escape'];
		$this->autoReload = (bool) $options['auto_reload'];
		$this->cache = (string) $options['cache'];
		$this->charset = (string) $options['charset'];
		$this->debug = (bool) $options['debug'];
		$this->strict = (bool) $options['strict'];

		// load default extensions
		/*$this->extensions = array(
			'condition' => new Condition(),
			'form' => new Form(),
		);*/
	}

	/**
	 * Add an extension.
	 *
	 * @return Component
	 * @param Extension $extenion
	 */
	public function addExtension(Extension $extension)
	{
		$this->extensions[$extension->getName()] = $extension;
		return $this;
	}

	/**
	 * Disable auto escaping of variables.
	 *
	 * @return Component
	 */
	public function disableAutoEscape()
	{
		$this->autoEscape = false;
		return $this;
	}

	/**
	 * Disable automatically reloading templates based on the modification date.
	 *
	 * @return Component
	 */
	public function disableAutoReload()
	{
		$this->autoReload = false;
		return $this;
	}

	/**
	 * Disable debug mode.
	 *
	 * @return Component
	 */
	public function disableDebug()
	{
		$this->debug = false;
		return $this;
	}

	/**
	 * Disable strict parsing of variables.
	 *
	 * @return Component
	 */
	public function disableStrict()
	{
		$this->strict = false;
		return $this;
	}

	/**
	 * Enable auto escaping of variables.
	 *
	 * @return Component
	 */
	public function enableAutoEscape()
	{
		$this->autoEscape = true;
		return $this;
	}

	/**
	 * Enable auto reload.
	 *
	 * @return Component
	 */
	public function enableAutoReload()
	{
		$this->autoReload = true;
		return $this;
	}

	/**
	 * Enable debug mode.
	 *
	 * @return Component
	 */
	public function enableDebug()
	{
		$this->debug = true;
		return $this;
	}

	/**
	 * Enable strict parsing of variables.
	 *
	 * @return Component
	 */
	public function enableStrict()
	{
		$this->strict = true;
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
		// @todo not yet implemented
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
		return (filemtime((string) $template) < (int) $time);
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
	 * Is strict parsing of variables enabled.
	 *
	 * @return bool
	 */
	public function isStrict()
	{
		return $this->strict;
	}

	/**
	 * Remove an extension from the list.
	 *
	 * @return Component
	 * @param string $name The name of the extension.
	 */
	public function removeExtension($name)
	{
		unset($this->extensions[$name]);
		return $this;
	}

	/**
	 * Set caching directory.
	 *
	 * @return Component
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
	 * @return Component
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
	 * @return Component
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
