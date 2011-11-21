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
use Spoon\Template\Writer;

/**
 * Class used to convert some tokens related variables into PHP code
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class VariableNode
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
	 * @todo document me
	 */
	protected function build(Writer $writer)
	{
		$output = '$this->getVar($context, array(';
		$count = count($this->variable);

		foreach($this->variable as $key => $value)
		{
			// function with arguments
			if(is_array($value))
			{
				$output .= "'" . key($value) .'\' => array(' . implode(', ', $value[key($value)]) . ')';
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

		$writer->write('echo ' . $output . ';' . "\n", $this->stream->getCurrent()->getLine());
	}

	/**
	 * Compile the first variable you come across and return its PHP code string value.
	 *
	 * @param Spoon\Template\Writer $writer
	 */
	public function compile(Writer $writer)
	{
		$this->processName();
		$this->build($writer);
	}

	/**
	 * Processes the tokens and builds (or extends) a list of arguments.
	 *
	 * @todo there still seems to be some issue with arguments.
	 *
	 * @param array[optional] $arguments
	 * @return array
	 */
	protected function processArguments(array $arguments = array())
	{
		$token = $this->stream->getCurrent();

		// allowed tokens are NUMBER, STRING
		if(!$token->test(Token::NUMBER) && !$token->test(Token::STRING))
		{
			// subvariable
			if($token->test(Token::NAME) && substr($token->getValue(), 0, 1) == '$')
			{
				$subVariable = new SubVariable($this->stream, $this->environment);
				$arguments[] = $subVariable->compile();

				$token = $this->stream->previous();
			}

			// not allowed
			else
			{
				throw new SyntaxError(
					'Modifier arguments need to be either integers, strings or subvars',
					$token->getLine(),
					$this->stream->getFilename()
				);
			}
		}

		// string argument
		if($token->getType() === Token::STRING)
		{
			$arguments[] = "'" . str_replace("'", "\'", ($token->getValue())) . "'";
		}

		// integer argument
		elseif($token->getType() == Token::NUMBER)
		{
			$arguments[] = $token->getValue();
		}

		// next
		$token = $this->stream->next();
		$value = $token->getValue();

		// ")" end of the arguments
		if($token->test(Token::PUNCTUATION, ')'))
		{
			// skip ')'
			$this->stream->next();
			return $arguments;
		}

		// comma separated
		elseif($token->test(Token::PUNCTUATION, ','))
		{
			// skip ','
			$this->stream->next();
			return $this->processArguments($arguments);
		}

		else
		{
			/*
			 * @todo allow concatenation for subvariables
			 * eg. {$name|modifier($arg . $arg2)}
			 * eg. {$name|modifier($arg . 'string' . $arg2)}
			 */
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
		$token = $this->stream->getCurrent();
		$value = $token->getValue();

		// this part needs to be a NAME or NUMBER
		if($token->test(Token::NAME) && $value[0] != '$' || $token->test(Token::NUMBER))
		{
			// add to list of keys
			$key = $token->getValue();
			$token = $this->stream->next();

			// allowed next values are "." or "(" or VAR_END
			$value = $token->getValue();

			// new key
			if($token->test(Token::PUNCTUATION, '.'))
			{
				$this->variable[] = $key;
				$this->stream->next();
				$this->processKey();
			}

			// indication the next part is a modifier
			elseif($token->test(Token::PUNCTUATION, '|'))
			{
				$this->variable[] = $key;
				$this->stream->next();
				$this->processModifier();
			}

			// indication arguments start here
			elseif($token->test(Token::PUNCTUATION, '('))
			{
				// skip '('
				$this->stream->next();
				$arguments = $this->processArguments();
				$this->variable[] = array($key => $arguments);
				$token = $this->stream->getCurrent();

				// more keys
				if($token->test(Token::PUNCTUATION, '.'))
				{
					$this->stream->next();
					$this->processKey();
				}

				// more modifiers
				elseif($token->test(Token::PUNCTUATION, '|'))
				{
					$this->stream->next();
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

		/*
		 * Variable keys may not start with "$" (eg {$name.$name}
		 * The "$" is only allowed for the first part of the variable.
		 */
		else
		{
			throw new SyntaxError(
				'Variable keys may not start with $',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}
	}

	/**
	 * Processes the modifier and its possible arguments.
	 */
	protected function processModifier()
	{
		$token = $this->stream->getCurrent();

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
			$this->stream->next();
			$this->processModifier();
		}

		// arguments
		elseif($token->test(Token::PUNCTUATION, '('))
		{
			// skip '('
			$this->stream->next();
			$arguments = $this->processArguments();
			$this->modifiers[] = array($modifier => $arguments);

			// another modifier
			$token = $this->stream->getCurrent();
			if($token->test(Token::PUNCTUATION, '|'))
			{
				$this->stream->next();
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
		$token = $this->stream->getCurrent();

		// must be a name token
		$this->stream->expect(Token::NAME);
		$this->variable[] = $token->getValue();

		$token = $this->stream->next();

		// allowed values are: "|" or "." or VAR_END
		$value = $token->getValue();

		// expecting keys
		if($token->test(Token::PUNCTUATION, '.'))
		{
			// skip '.'
			$this->stream->next();
			$this->processKey();
		}

		// expecting modifiers
		elseif($token->test(Token::PUNCTUATION, '|'))
		{
			// skip '|'
			$this->stream->next();
			$this->processModifier();
		}

		// only thing allowed now is the end of the variable
		else
		{
			$this->stream->expect(Token::VAR_END);
		}
	}
}
