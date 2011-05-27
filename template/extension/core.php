<?php

namespace spoon\template\extension;
use spoon\template\Extension;

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
	 * @param string $source The string you want to strip the code from.
	 */
	public function stripCode($source)
	{
		return $source = preg_replace('/\<\?(php)?(.*)\?\>/si', '', $source);
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
