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
 * This class represents a single token with some extra information such as its line number and
 * type.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Token
{
	/**
	 * List of token types.
	 *
	 * @var int
	 */
	const EOF = -1;
	const TEXT = 0;
	const BLOCK_START = 1;
	const BLOCK_END = 2;
	const VAR_START = 3;
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
		$string .= '[type]: ' . $this->typeToString($this->type) . "\n";
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

	/**
	 * Test some assertions.
	 *
	 * @todo refactor this method
	 *
	 * @param string $type
	 * @param array[optional] $values
	 * @return bool
	 */
	public function test($type, $values = null)
	{
		if($this->type === $type)
		{
			if(is_array($values))
			{
				return in_array($this->value, $values);
			}

			elseif($values !== null)
			{
				return $this->value === $values;
			}

			return true;
		}

		return false;
	}

	/**
	 * Returns the type as a string representation.
	 *
	 * @return string
	 * @param int $type
	 */
	public static function typeToString($type)
	{
		switch($type)
		{
			case self::EOF:
				return 'EOF';
				break;

			case self::TEXT:
				return 'TEXT';
				break;

			case self::BLOCK_START:
				return 'BLOCK_START';
				break;

			case self::BLOCK_END:
				return 'BLOCK_END';
				break;

			case self::VAR_START:
				return 'VAR_START';
				break;

			case self::VAR_END:
				return 'VAR_END';
				break;

			case self::NAME:
				return 'NAME';
				break;

			case self::NUMBER:
				return 'NUMBER';
				break;

			case self::STRING:
				return 'STRING';
				break;

			case self::OPERATOR:
				return 'OPERATOR';
				break;

			case self::PUNCTUATION:
				return 'PUNCTUATION';
				break;

			default:
				throw new Exception(sprintf('There is no token type "%s"', $type));
		}
	}
}
