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
	 * Current token number.
	 *
	 * @var int
	 */
	protected $current = 0;

	/**
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
	 * @param Spoon\Template\Environment $environment
	 * @param string[optional] $filename
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

	/**
	 * Checks if the current token is what you expect it to be.
	 *
	 * @param int $type
	 * @param mixed[optional] $value
	 * @return Spoon\Template\Token
	 */
	public function expect($type, $value = null)
	{
		$token = $this->tokens[$this->current];

		if(!$token->test($type, $value))
		{
			$line = $token->getLine();
			$message = $value ? sprintf(' with value "%s"', $value) : '';

			throw new SyntaxError(
				sprintf(
					'Unexpected token "%s" of value "%s" ("%s" expected%s)',
					Token::typeToString($token->getType(), $line),
					$token->getValue(),
					Token::typeToString($type),
					$message
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
	 * @return Spoon\Template\Token
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
	 * Is this the end of the token stream.
	 *
	 * @return bool
	 */
	public function isEof()
	{
		return $this->tokens[$this->current]->getType() === Token::EOF;
	}

	/**
	 * Look at another token without changing the current positon.
	 *
	 * @return Spoon\Template\Token
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
	 * @return Spoon\Template\Token
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

	/**
	 * Retrieve the previous token.
	 *
	 * @return Spoon\Template\Token
	 */
	public function previous()
	{
		if(!isset($this->tokens[$this->current - 1]))
		{
			throw new SyntaxError('You are already at the first token', -1, $this->filename);
		}

		$this->current--;
		return $this->tokens[$this->current];
	}
}
