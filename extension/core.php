<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace spoon\template\extension;
use spoon\template\Extension;

/**
 * This is the core extension. It will perform the following tasks:
 * - strip comments
 * - strip (php) code
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Core extends Extension
{
	const NAME = 'core';

	/**
	 * Parses the extension using the source string.
	 *
	 * @return string
	 * @param string $source The basic string to start from.
	 */
	public function parse($source)
	{
		$source = $this->stripComments($source);
		$source = $this->stripCode($source);
		return $source;
	}

	/**
	 * Strips php code between tags from the source.
	 *
	 * @return string
	 * @param string $source The string you want to strip the php code from.
	 */
	public function stripCode($source)
	{
		return preg_replace('/\<\?(php)?(.*)\?\>/si', '', $source);
	}

	/**
	 * Strips template comments from the source.
	 *
	 * @return string
	 * @param string $source The string you want the template comments to be removed from.
	 */
	public function stripComments($source)
	{
		$count = 1;
		while($count > 0)
		{
			$source = preg_replace('/\{\*(?!.*?\{\*).*?\*\}/s', '', $source, -1, $count);
		}

		return $source;
	}
}
