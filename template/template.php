<?php

namespace spoon\template;

class Template extends Component
{
	/**
	 * Stack of variables & their replace values
	 *
	 * @var array
	 */
	protected $variables;

	/**
	 * Assign values to variables.
	 *
	 * @return Template
	 * @param mixed $variable  The key to search for or an array/object with keys & values.
	 * @param mixed[optional] $value  The value to replace the key with. Optional if first is array/object.
	 */
	public function assign($variable, $value = null)
	{
		// regular function use
		if($value !== null && $variable != '') $this->variables[(string) $variable] = $value;

		// only 1 argument
		else
		{
			// not an array
			if(!is_array($variable) && !is_object($variable))
			{
				trigger_error('If you only provide the first argument it needs to be an array/object.', E_WARNING);
			}

			// loop array & add to variables
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
	 * @return mixed
	 * @param string $variable The variable you wish to retrieve from the stack.
	 */
	public function get($variable)
	{
		if(array_key_exists((string) $variable, $this->variables))
		{
			return $this->variables[(string) $variable];
		}
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
		if(!file_exists($template) || is_readable($template))
		{
			throw new Exception(sprintf('The template "%s" does NOT exist.', $template));
		}

		// determine cache location
		$cache = $this->getCacheFilename($template);

		// the cache file doesn't exist or it's outdated
		if(!file_exists($cache) || ($this->autoReload() && !$this->isChanged($template, filemtime($cache))))
		{
			// configuration options
			$options = array(
				'auto_escape' => $this->autoEscape,
				'auto_reload' => $this->autoReload,
				'cache' => $this->cache,
				'charset' => $this->charset,
				'debug' => $this->debug,
				'strict' => $this->strict
			);

			// write cached php file
			$compiler = new Compiler($template, $options);
			$compiler->setExtensions($this->getExtensions());
			$compiler->write();
		}

		require $cache;
	}
}
