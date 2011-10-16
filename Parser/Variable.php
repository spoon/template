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
 * @todo write some docs
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Variable
{
	/**
	 * Token stream.
	 *
	 * @var TokenStream
	 */
	protected $stream;

	// @todo document
	protected $environment;
	protected $variable;
	protected $modifiers = array();

	public function __construct(TokenStream $stream, Environment $environment)
	{
		$this->stream = $stream;
		$this->environment = $environment;
	}

	protected function build($subVariable = false)
	{
		// starting output
		$output = '$this->getVar($context, array(';

		// voeg alle variabelen toe
		$count = count($this->variable);
		foreach($this->variable as $key => $value)
		{
			// functions with arguments
			if(is_array($value))
			{
				$output .= "'";
				$output .= key($value) .'\' => array(' . implode(', ', $value[key($value)]) . ')';
			}

			// just method/key element
			else
			{
				$output .= "'";
				$output .= $value;
				$output .= "'";
			}

			// more keys follow?
			if($key < $count - 1)
			{
				$output .= ', ';
			}

			else $output .= ')';
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

			$output = 'call_user_func_array($this->environment->getModifier(\'' . $modifier . '\'), array(';
			$output .= implode(', ', $arguments);
			$output .= '))';
		}

		// echo for regular variables
		if(!$subVariable)
		{
			$output = 'echo ' . $output . ';' . "\n";
		}

		return $output;
	}

	public function compile()
	{
		$this->processName();
		return $this->build();
	}

	protected function processName()
	{
		$token = $this->stream->next();

		// must be a name token
		$this->stream->expect(Token::NAME);
		$this->variable[] = $token->getValue();

		$token = $this->stream->next();


		// allowed values are: "|" or "." or VAR_END
		$value = $token->getValue();

		// we verwachten dat er één of meerdere key elementen volgen
		if($token->test(Token::PUNCTUATION, '.'))
		{
			$this->processKey();
		}

		// we verwachten dat er één of meerdere modifiers volgen
		elseif($token->test(Token::PUNCTUATION, '|'))
		{
			$this->processModifier();
		}

		// we verwachten het einde van de variabele
		else
		{
			$this->stream->expect(Token::VAR_END);
		}
	}

	protected function processKey()
	{
		// skip the "."
		$token = $this->stream->next();

		// the next part needs to be a name
		$this->stream->expect(Token::NAME);

		// de lexer vangt niet op dat een modifier name geen "$" mag bevatten
		if(strpos($token->getValue(), '$') !== false)
		{
			throw new SyntaxError(
				'Variable keys may not start with "$"',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}

		// voeg toe aan modifiers
		$key = $token->getValue();
		$token = $this->stream->next();

		// allowed next values are "." or "(" VAR_END
		$value = $token->getValue();

		// new key
		if($token->test(Token::PUNCTUATION, '.'))
		{
			$this->variable[] = $key;
			$this->processKey();
		}

		// modifiers, get out of here
		elseif($token->test(Token::PUNCTUATION, '|'))
		{
			$this->variable[] = $key;
			$this->processModifier();
		}

		// arguments
		elseif($token->test(Token::PUNCTUATION, '('))
		{
			$arguments = $this->processArguments();
			$this->variable[] = array($key => $arguments);

			// volgt er nog een modifier of ist gedaan?
			$token = $this->stream->getCurrent();
			if($token->test(Token::PUNCTUATION, '.'))
			{
				$this->processKey();
			}

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

	protected function processModifier()
	{
		// skip the "|"
		$token = $this->stream->next();

		// the next part needs to be a name
		$this->stream->expect(Token::NAME);

		// de lexer vangt niet op dat een modifier name geen "$" mag bevatten
		if(strpos($token->getValue(), '$') !== false)
		{
			throw new SyntaxError(
				'Variable modifier may not start with "$"',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}

		// voeg toe aan modifiers
		$modifier = $token->getValue();
		$token = $this->stream->next();

		// allowed next values are "|" or "(" VAR_END
		$value = $token->getValue();

		// new modifier
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

			// volgt er nog een modifier of ist gedaan?
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

	protected function processArguments(array $arguments = array())
	{
		// skip the first char "(" or possibly ","
		$token = $this->stream->next();


		// allowed tokens are NUMBER, STRING
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

		elseif($token->test(Token::PUNCTUATION, ','))
		{
			return $this->processArguments($arguments);
		}

		else
		{
			throw new SyntaxError(
				'The modifier arguments seem malformed',
				$token->getLine(),
				$this->stream->getFilename()
			);
		}
	}
}
