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
 * Class used to convert some tokens related variables into PHP code
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Variable
{
	/**
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	/**
	 * List of modifiers (and their arguments) for this variable.
	 *
	 * @var array
	 */
	protected $modifiers = array();

	/**
	 * @var Spoon\Template\TokenStream
	 */
	protected $stream;

	/**
	 * List of chunks for this variable optionally arguments (in case used as methods)
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
	 * @param bool[optional] $subVariable If true then no echo statement will be prepended.
	 * @return string
	 */
	protected function build($subVariable = false)
	{
		$output = '$this->getVar($context, array(';
		$count = count($this->variable);

		// scan each chunk
		foreach($this->variable as $key => $value)
		{
			// function with arguments
			if(is_array($value))
			{
				$output .= "'" . $output .= key($value) .'\' => array(' . implode(', ', $value[key($value)]) . ')';
			}

			// just method/key element
			else
			{
				$output .= "'" . $value . "'";
			}

			// last key
			$output .= ($key < $count - 1) ? ', ' : ')';
		}

		$output .= ')';

		foreach($this->modifiers as $modifier)
		{
			// the variable itself is always added as an argument
			$arguments = array($output);

			// modifier consist of name & arguments
			if(is_array($modifier))
			{
				$arguments = array_merge($arguments, $modifier[key($modifier)]);
				$modifier = key($modifier);
			}

			$output = 'call_user_func_array($this->environment->getModifier(\'' . $modifier . '\'), ';
			$output .= 'array(' . implode(', ', $arguments) . '))';
		}

		// prepend echo
		if(!$subVariable)
		{
			$output = 'echo ' . $output . ';' . "\n";
		}

		return $output;
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
	 * Processes the tokens and builds (or extends) a list of arguments.
	 *
	 * @todo needs to allow subvars and parse them within these arguments (not only int/string)
	 *
	 * @param array[optional] $arguments
	 * @return array
	 */
	protected function processArguments(array $arguments = array())
	{
		// skip the first char "(" or possibly ","
		$token = $this->stream->next();

		// allowed tokens are NUMBER, STRING
		// @todo allow subvars
		if(!$token->test(Token::NUMBER) && !$token->test(Token::STRING))
		{
			// @todo adjust message when subvars are allowed
			throw new SyntaxError(
				'Modifier arguments need to be either integers or strings',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}

		// string argument
		if($token->getType() === Token::STRING)
		{
			$arguments[] = "'" . str_replace("'", "\'", ($token->getValue())) . "'";
		}

		// integer argument
		else
		{
			$arguments[] = $token->getValue();
		}

		// next
		$token = $this->stream->next();
		$value = $token->getValue();

		// ")" end of the arguments
		if($token->test(Token::PUNCTUATION, ')'))
		{
			$this->stream->next();
			return $arguments;
		}

		// comma separated
		elseif($token->test(Token::PUNCTUATION, ','))
		{
			return $this->processArguments($arguments);
		}

		else
		{
			throw new SyntaxError(
				'The arguments seem malformed. Check your syntax.',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}
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
		}

		// the lexer doesn't catch keys starting with a "$"
		if(strpos($token->getValue(), '$') !== false)
		{
			throw new SyntaxError(
				'Variable keys may not start with "$"',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}

		// add to list of keys
		$key = $token->getValue();
		$token = $this->stream->next();

		// allowed next values are "." or "(" or VAR_END
		$value = $token->getValue();

		// new key
		if($token->test(Token::PUNCTUATION, '.'))
		{
			$this->variable[] = $key;
			$this->processKey();
		}

		// indication the next part is a modifier
		elseif($token->test(Token::PUNCTUATION, '|'))
		{
			$this->variable[] = $key;
			$this->processModifier();
		}

		// indication arguments start here
		elseif($token->test(Token::PUNCTUATION, '('))
		{
			$arguments = $this->processArguments();
			$this->variable[] = array($key => $arguments);
			$token = $this->stream->getCurrent();

			// more keys
			if($token->test(Token::PUNCTUATION, '.'))
			{
				$this->processKey();
			}

			// more modifiers
			elseif($token->test(Token::PUNCTUATION, '|'))
			{
				$this->processModifier();
			}
		}

		// end variable
		else
		{
			$this->variable[] = $key;
			$this->stream->expect(Token::VAR_END);
		}
	}

	/**
	 * Processes the modifier and its possible arguments.
	 */
	protected function processModifier()
	{
		// skip the "|"
		$token = $this->stream->next();

		// the next part needs to be a name
		$this->stream->expect(Token::NAME);

		// the lexer doesn't catch modifier names starting with "$"
		if(strpos($token->getValue(), '$') !== false)
		{
			throw new SyntaxError(
				'Variable modifier may not start with "$"',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}

		// add to modifiers
		$modifier = $token->getValue();
		$token = $this->stream->next();

		// allowed next values are "|" or "(" or VAR_END
		$value = $token->getValue();

		// another modifier
		if($token->test(Token::PUNCTUATION, '|'))
		{
			$this->modifiers[] = $modifier;
			$this->processModifier();
		}

		// arguments
		elseif($token->test(Token::PUNCTUATION, '('))
		{
			$arguments = $this->processArguments();
			$this->modifiers[] = array($modifier => $arguments);

			// another modifier
			$token = $this->stream->getCurrent();
			if($token->test(Token::PUNCTUATION, '|'))
			{
				$this->processModifier();
			}
		}

		// end variable
		else
		{
			$this->modifiers[] = $modifier;
			$this->stream->expect(Token::VAR_END);
		}
	}

	/**
	 * Processes the first key element of variable. Different rules apply to the first part.
	 */
	protected function processName()
	{
		$token = $this->stream->next();

		// must be a name token
		$this->stream->expect(Token::NAME);
		$this->variable[] = $token->getValue();

		$token = $this->stream->next();

		// allowed values are: "|" or "." or VAR_END
		$value = $token->getValue();

		// expecting keys
		if($token->test(Token::PUNCTUATION, '.'))
		{
			$this->processKey();
		}

		// expecting modifiers
		elseif($token->test(Token::PUNCTUATION, '|'))
		{
			$this->processModifier();
		}

		// only thing allowed now is the end of the variable
		else
		{
			$this->stream->expect(Token::VAR_END);
		}
	}
}
