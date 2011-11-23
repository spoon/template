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
use Spoon\Template\SyntaxError;
use Spoon\Template\Token;
use Spoon\Template\TokenStream;
use Spoon\Template\Environment;
use Spoon\Template\Writer;

/**
 * @todo document me
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class IfNode extends Node
{
	/**
	 * Counter for all opened brackets.
	 *
	 * @var int
	 */
	protected $brackets;

	/**
	 * Starting line of the if node.
	 *
	 * @var int
	 */
	protected $line;

	/**
	 * @var string
	 */
	protected $output;

	/**
	 * Writes the compiled PHP code to the writer object.
	 *
	 * @param Spoon\Template\Writer $writer
	 */
	public function compile(Writer $writer)
	{
		$this->line = $this->stream->next()->getLine();

		$this->process();
		$this->output = trim($this->output);
		$this->output = str_replace(array('( ', '! '), array('(', '!'), $this->output);

		if($this->brackets != 0)
		{
			// @todo create syntax error
			exit('Not all opened brackets were properly closed');
		}

		$writer->write('if(' . $this->output . "):\n");
		$writer->indent();
	}

	/**
	 * Processes the if syntax.
	 *
	 * @todo document me inline
	 */
	protected function process()
	{
		$token = $this->stream->getCurrent();

		if($token->test(Token::NAME))
		{
			if(substr($token->getValue(), 0, 1) != '$')
			{
				// @todo create syntax error
				exit('Variables need to start with $');
			}

			else
			{
				$subVariable = new SubVariable($this->stream, $this->environment);
				$this->output .= ' ' . $subVariable->compile();
				$this->stream->previous();
			}
		}

		elseif($token->test(Token::NUMBER))
		{
			$this->output .= ' ' . $token->getValue();
		}

		elseif($token->test(Token::OPERATOR))
		{
			/*
			 * Expression regex pattern for:
			 * or, and, ==, !=, <, >, >=, <=, +, -, *, /, %
			 */
			switch($token->getValue())
			{
				case 'or':
					$this->output .= ' ||';
					break;

				case 'and':
					$this->output .= ' &&';
					break;

				case 'not':
					$this->output .= ' !';
					break;

				case '==':
				case '!=':
				case '>':
				case '>=':
				case '<':
				case '<=':
				case '*':
				case '+':
				case '-':
				case '/':
				case '%':
				case '~':
					$this->output .= ' ' . $token->getValue();
					break;

				default:
					// @todo create syntax error
					var_dump('unknown operator');
					var_dump($token->getValue());
					exit;
			}
		}

		elseif($token->test(Token::PUNCTUATION, '('))
		{
			$this->output .= ' (';
			$this->brackets += 1;
		}

		elseif($token->test(Token::PUNCTUATION, ')'))
		{
			$this->output .= ')';
			$this->brackets -= 1;
		}

		elseif($token->test(Token::STRING))
		{
			$this->output .= " '" . str_replace("'", "\'", ($token->getValue())) . "'";
		}

		$token = $this->stream->next();
		if(!$token->test(Token::BLOCK_END))
		{
			$this->process();
		}
	}
}
