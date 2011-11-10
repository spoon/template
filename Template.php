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
	 * @return Spoon\Template\Template
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
	 * Method used to fetch the actual value based on the context and a list of keys.
	 *
	 * @todo rethink the name
	 * @todo is this the class for this method?
	 * @todo refactoring needed?
	 *
	 * @param array $context
	 * @param array $elements
	 * @return mixed
	 */
	protected function getVar(array $context, array $elements)
	{
		// only 1 element
		if(count($elements) == 1)
		{
			if(array_key_exists($elements[0], $context))
			{
				return $context[$elements[0]];
			}

			return null;
		}

		$i = 1;
		foreach($elements as $index => $value)
		{
			/*
			 * First element is always special, because this is always an element in the context
			 * array
			 */

			// first element is custom
			if($i == 1)
			{
				$variable = $context[$value];
			}

			// other (regular) elements
			else
			{
				// an array
				if(is_array($variable))
				{
					$variable = $variable[$value];
				}

				// an object
				elseif(is_object($variable))
				{
					$arguments = (is_array($value)) ? $value : null;
					$element = (is_array($value)) ? $index : $value;

					// public property exists (and is not null)
					if(property_exists($variable, $element) && isset($variable->$element))
					{
						$variable = $variable->$element;
					}

					// public method exists
					elseif(method_exists($variable, $element) && is_callable(array($variable, $element)))
					{
						if($arguments !== null)
						{
							$variable = call_user_func_array(array($variable, $element), $arguments);
						}

						else $variable = $variable->$element();
					}

					// public getter method exists
					elseif(method_exists($variable, 'get' . ucfirst($element)) && is_callable(array($variable, 'get' . ucfirst($element))))
					{
						$method = 'get' . ucfirst($element);
						if($arguments !== null)
						{
							$variable = call_user_func_array(array($variable, $method), $arguments);
						}

						else $variable = $variable->$method();
					}

					// everythine failed
					else $variable = null;
				}

				// not an object, nor array
				else return null;
			}
			$i++;
		}

		return $variable;
	}

	/**
	 * Remove a variable from the list.
	 *
	 * @param string $variable The key to remove.
	 * @return Spoon\Template\Template
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
	 * @param array[optional] $variables An alternative list of variables to use.
	 */
	public function render($filename, array $variables = null)
	{
		// template exists and is readable
		if(!file_exists($filename) || !is_readable($filename))
		{
			throw new Exception(sprintf('The template "%s" does not exist.', $filename));
		}

		// determine cache location
		$cache = $this->environment->getCache() . '/' . $this->environment->getCacheFilename($filename);

		// the cache file doesn't exist or is outdated
		if(!file_exists($cache) || ($this->environment->isAutoReload() && $this->environment->isChanged($filename, filemtime($cache))))
		{
			$compiler = new Compiler($this, $filename);
			$compiler->write();
		}

		require $cache;
		$class = 'Spoon\Template\S' . basename(substr($cache, 0, -8)) . '_Template';
		$instance = new $class($this->environment);
		$instance->display(($variables !== null) ? $variables : $this->variables);
	}
}
