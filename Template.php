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
 * Class that holds the template variables.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Template
{
	const VERSION = 0.1;

	/**
	 * The configuration for this template.
	 *
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	/**
	 * Stack of variables & their replace values
	 *
	 * @var array
	 */
	protected $variables;

	/**
	 * @param Spoon\Template\Environment $environment
	 */
	public function __construct(Environment $environment)
	{
		$this->environment = $environment;
	}

	/**
	 * Assign values to variables.
	 *
	 * @param mixed $variable The key to search for or an array/object with keys & values.
	 * @param mixed[optional] $value The value to replace the key with. Optional if first is array/object.
	 * @return Spoon\Template
	 */
	public function assign($variable, $value = null)
	{
		// regular function use
		if($value !== null && $variable != '') $this->variables[(string) $variable] = $value;

		// only 1 argument
		else
		{
			if(!is_array($variable) && !is_object($variable))
			{
				trigger_error('If you only provide the first argument it needs to be an array/object.', E_WARNING);
			}

			foreach($variable as $key => $value)
			{
				$this->variables[$key] = $value;
			}
		}

		return $this;
	}

	/**
	 * Fetch an already assigned variable.
	 *
	 * @param string $variable The variable you wish to retrieve from the stack.
	 * @return mixed
	 */
	public function get($variable)
	{
		if(array_key_exists((string) $variable, $this->variables))
		{
			return $this->variables[(string) $variable];
		}
	}

	/**
	 * Returns the environment configuration object.
	 *
	 * @return Spoon\Template\Environment
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}

	/**
	 * Remove a variable from the list.
	 *
	 * @return Template
	 * @param string $variable The key to remove.
	 */
	public function remove($variable)
	{
		unset($this->variables[(string) $variable]);
		return $this;
	}

	/**
	 * Render the template using the assigned variables.
	 *
	 * @param string $template The location of the template file.
	 */
	public function render($template)
	{
		// template exists and is readable
		if(!file_exists($template) || !is_readable($template))
		{
			throw new Exception(sprintf('The template "%s" does NOT exist.', $template));
		}

		// determine cache location
		$cache = $this->environment->getCache() . '/' . $this->environment->getCacheFilename($template);

		// the cache file doesn't exist or it's outdated
		if(!file_exists($cache) || ($this->environment->isAutoReload() && $this->environment->isChanged($template, filemtime($cache))))
		{
			$compiler = new Compiler($this, $template);
			$compiler->write();
		}

		require $cache;
	}
}
