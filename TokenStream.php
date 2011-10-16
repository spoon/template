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
use Spoon\Template\Token;

/**
 * Represents a token stream.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class TokenStream
{
	/**
	 * Current token.
	 *
	 * @var int
	 */
	protected $current = 0;

	/**
	 * Filename.
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * The list of all tokens.
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * Constructor.
	 *
	 * @param Environment $environment
	 */
	public function __construct(array $tokens, $filename = null)
	{
		$this->tokens = $tokens;
		$this->filename = $filename;
	}

	/**
	 * Returns a string representation of the token stream.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return implode("\n", $this->tokens);
	}

	// @todo refactor me
	public function expect($type, $value = null, $message = null)
	{
		$token = $this->tokens[$this->current];

		if(!$token->test($type, $value))
		{
			$line = $token->getLine();
			$test = $value ? sprintf(' with value "%s"', $value) : '';

			throw new SyntaxError(
				sprintf(
					'Unexpected token "%s" of value "%s" ("%s" expected%s)',
					Token::typeToString($token->getType(), $line),
					$token->getValue(),
					Token::typeToString($type),
					$test
				),
				$line,
				$this->filename
			);
		}

		return $this->getCurrent();
	}

	/**
	 * Retrieve the current token.
	 *
	 * @return Token
	 */
	public function getCurrent()
	{
		return $this->tokens[$this->current];
	}

	/**
	 * Retrieve the filename for this stream.
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Is this the end of the stream.
	 *
	 * @return bool
	 */
	public function isEof()
	{
		return $this->tokens[$this->current]->getType() === Token::EOF;
	}

	/**
	 * Look to another token without changing the current positon.
	 *
	 * @return Token
	 * @param int[optional] $number
	 */
	public function look($number = 1)
	{
		if(!isset($this->tokens[$this->current + $number]))
		{
			throw new SyntaxError('Unexpected end of template', -1, $this->filename);
		}

		return $this->tokens[$this->current + $number];
	}

	/**
	 * Retrieve the next token.
	 *
	 * @return Token
	 */
	public function next()
	{
		if(!isset($this->tokens[$this->current + 1]))
		{
			throw new SyntaxError('Unexpected end of template', -1, $this->filename);
		}

		$this->current++;
		return $this->tokens[$this->current];
	}
}
