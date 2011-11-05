<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace Spoon\Template\Parser;
use Spoon\Template\Environment;
use Spoon\Template\SyntaxError;
use Spoon\Template\TokenStream;
use Spoon\Template\Token;

/**
 * Class used to convert some tokens related subvariables into PHP code. The rules for subvariables
 * are a lot more restrictive than regular variables.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class SubVariable
{
	/**
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	/**
	 * @var Spoon\Template\TokenStream
	 */
	protected $stream;

	/**
	 * List of chunks for this variable.
	 *
	 * @var array
	 */
	protected $variable = array();

	/**
	 * @param Spoon\Template\TokenStream $stream
	 * @param Spoon\Template\Environment $environment
	 */
	public function __construct(TokenStream $stream, Environment $environment)
	{
		$this->stream = $stream;
		$this->environment = $environment;
	}

	/**
	 * Builds the variable string.
	 *
	 * @return string
	 */
	protected function build()
	{
		$output = '$this->getVar($context, array(';
		$count = count($this->variable);

		// scan each chunk
		foreach($this->variable as $key => $value)
		{
			$output .= "'" . $value . "'";

			// last key
			$output .= ($key < $count - 1) ? ', ' : ')';
		}

		return $output . ')';
	}

	/**
	 * Compile the first variable you come across and return its PHP code string value.
	 *
	 * @return string
	 */
	public function compile()
	{
		$this->processName();
		return $this->build();
	}

	/**
	 * Processes each key element based on a set of allowed rules.
	 */
	protected function processKey()
	{
		// skip the "."
		$token = $this->stream->next();

		// the next part needs to be a name or number
		if(!$token->test(Token::NAME) && !$token->test(Token::NUMBER))
		{
			// @todo throw syntax error
			// @todo reproduce this in unit tests
		}

		// the lexer doesn't catch keys starting with a "$"
		if(strpos($token->getValue(), '$') !== false)
		{
			$this->stream->previous();
			return;

			// @todo remove me
			/*throw new SyntaxError(
				'Variable keys may not start with "$"',
				$token->getLine(),
				$this->stream->getFilename()
			);*/
		}

		// add to list of keys
		$key = $token->getValue();
		$token = $this->stream->next();

		/*
		 * If the value is not "." then we assume there are no further parts for this subvariable
		 * so we stop parsing.
		 */
		$value = $token->getValue();

		// new key
		if($token->test(Token::PUNCTUATION, '.'))
		{
			$this->variable[] = $key;
			$this->processKey();
		}

		// end subvariable
		else
		{
			$this->variable[] = $key;
		}
	}

	/**
	 * Processes the first key element of variable. Different rules apply to the first part.
	 */
	protected function processName()
	{
		$token = $this->stream->getCurrent();

		// must be a name token
		$this->stream->expect(Token::NAME);
		$this->variable[] = substr($token->getValue(), 1);

		$token = $this->stream->next();

		/*
		 * If this token is a "." and only if the next one is a name token, we're going to
		 * consider keep looking for other chunks.
		 */
		// @todo the next token may also be a number (eg $users.0.hobbies) which doesn't work atm
		if($token->test(Token::PUNCTUATION, '.') && $this->stream->look()->test(Token::NAME))
		{
			$this->processKey();
		}
	}
}
