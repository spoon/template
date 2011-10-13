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
 * This class is used to do the actually writing of the compiled templates.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Writer
{
	/**
	 * Current indentation
	 *
	 * @var int
	 */
	protected $indentation = 0;

	/**
	 * Current line number
	 *
	 * @var int
	 */
	protected $line;

	/**
	 * String containing the source written so far
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * Fetch the written source.
	 *
	 * @return string
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * Indents one or more levels.
	 *
	 * @param int[optional] $level
	 * @return Spoon\Template\Writer
	 */
	public function indent($level = 1)
	{
		$this->indentation += (int) $level;
		return $this;
	}

	/**
	 * Outdents one or more levels.
	 *
	 * @param int[optional] $level
	 * @return Spoon\Template\Writer
	 */
	public function outdent($level = 1)
	{
		$this->indentation -= (int) $level;
		return $this;
	}

	/**
	 * Write to the source, optionally providing a line number.
	 *
	 * @param string $value
	 * @param int[optional] $line
	 * @return Spoon\Template\Writer
	 */
	public function write($value, $line = null)
	{
		// add current indentation
		$prefix = ($this->indentation > 0) ? str_repeat("\t", $this->indentation) : '';

		// only add line number once
		if($line !== null && $line > $this->line)
		{
			$this->line = (int) $line;
			$this->source .= $prefix . '// line ' . $this->line . "\n";
		}

		$this->source .= $prefix . (string) $value;
		return $this;
	}
}
