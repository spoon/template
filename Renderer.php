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
 * Class used as a base class for the rendered template.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Renderer extends Template
{
	/**
	 * Method used to fetch the actual value based on the context and a list of keys.
	 *
	 * @todo refactor?
	 * @todo $context should be optional and act that way by returning null if so
	 *
	 * @param array $context
	 * @param array $elements
	 * @return mixed
	 */
	public function getVar(array $context, array $elements)
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

					// public getter exists
					elseif(method_exists($variable, 'get' . ucfirst($element)) && is_callable(array($variable, 'get' . ucfirst($element))))
					{
						$method = 'get' . ucfirst($element);
						if($arguments !== null)
						{
							$variable = call_user_func_array(array($variable, $method), $arguments);
						}

						else $variable = $variable->$method();
					}

					// magic getter
					elseif(method_exists($variable, '__get') && is_callable(array($variable, '__get')))
					{
						if($element !== null) $variable = $variable->$element;
						else $variable = '';
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
}
