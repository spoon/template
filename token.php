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

/**
 * @todo write some explanation about what a token actually represents.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Token
{
	// @todo these values are not final
	const EOF = -1;
	const TEXT = 0;
	const BLOCK_START = 1;
	const VAR_START = 2;
	const BLOCK_END = 3;
	const VAR_END = 4;
	const NAME = 5;
	const NUMBER = 6;
	const STRING = 7;
	const OPERATOR = 8;
	const PUNCTUATION = 9;

	/**
	 * Line number this token starts on.
	 *
	 * @var int
	 */
	protected $line;

	/**
	 * Token type.
	 *
	 * @var int
	 */
	protected $type;

	/**
	 * Contains the actual value.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Constructor.
	 *
	 * @param int $type
	 * @param mixed $value
	 * @param int $line
	 */
	public function __construct($type, $value, $line)
	{
		$this->type = (int) $type;
		$this->value = $value;
		$this->line = (int) $line;
	}

	/**
	 * Returns a more readable token representation.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$string = '[line]: ' . $this->line . "\n";
		$string .= '[type]: ' . $this->type . "\n";
		$string .= '[value]: ' . $this->value . "\n";
		return $string;
	}

	/**
	 * Returns the line number.
	 *
	 * @return int
	 */
	public function getLine()
	{
		return $this->line;
	}

	/**
	 * Returns the type.
	 *
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Returns the value.
	 *
	 * @return mixed This method can return a string or an integer.
	 */
	public function getValue()
	{
		return $this->value;
	}

	public static function typeToString()
	{
		// @todo write me
	}
}
