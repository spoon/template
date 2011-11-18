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
 * Exception for syntax errors.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class SyntaxError extends \Exception
{
	/**
	 * @param string $message
	 * @param int[optional] $line
	 * @param string[optional] $filename
	 */
	public function __construct($message, $line = null, $filename = null)
	{
		// add line number
		if($line !== null)
		{
			$message .= " on line $line";
		}

		// add optional filename
		if($filename !== null)
		{
			$message .= ' in ' . $filename;
		}

		parent::__construct($message);
	}
}
