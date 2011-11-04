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
 * Class used to convert tokens concerning includes into PHP code.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class IncludeNode
{
	/**
	 * List of elements that need to be chained together.
	 *
	 * @var array
	 */
	protected $elements = array();

	/**
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	/**
	 * @var Spoon\Template\TokenStream
	 */
	protected $stream;

	/**
	 * @param Spoon\Template\TokenStream
	 * @param Spoon\Template\Environment
	 */
	public function __construct(TokenStream $stream, Environment $environment)
	{
		$this->stream = $stream;
		$this->environment = $environment;
	}

	/**
	 * @todo document me
	 * @todo refactor me
	 *
	 * @param Spoon\Template\Writer $writer
	 */
	protected function build(Writer $writer)
	{
		$output = '$this->render(';
		$count = count($this->elements);

		foreach($this->elements as $key => $element)
		{
			if(substr($element, 0, 1) == '$')
			{
				$output .= $element;
			}

			else
			{
				$output .= "'" . str_replace("'", "\'", $element) . "'";
			}

			if($key < $count - 1)
			{
				$output .= ' . ';
			}
		}

		$output .= ', $context);' . "\n";

		$writer->write($output, $this->stream->getCurrent()->getLine());
	}

	/**
	 * Writes the compiled PHP code to the writer object.
	 *
	 * @param Spoon\Template\Writer $writer
	 */
	public function compile(Writer $writer)
	{
		$this->stream->next();
		$this->process();
		$this->build($writer);
	}

	/**
	 * @todo refactor me, has some duplicated code
	 * @todo document me inline
	 */
	protected function process()
	{
		// {include [string] . [string] . [string] }
		$token = $this->stream->getCurrent();
		$value = $token->getValue();

		if($token->test(Token::STRING))
		{
			$this->elements[] = $value;
			$token = $this->stream->next();

			/*
			 * @todo check for duplicate code
			 */
			// next token needs to be "." or BLOCK_END
			if(!$token->test(Token::PUNCTUATION, '.') && !$token->test(Token::BLOCK_END))
			{
				throw new SyntaxError(
					sprintf(
						'Unexpected "%s" with value "%s"',
						Token::typeToString($token->getType()),
						$token->getValue()
					),
					$token->getLine(),
					$this->stream->getFilename()
				);
			}

			elseif($token->test(Token::PUNCTUATION, '.'))
			{
				$this->stream->next();
				$this->process();
			}
		}

		elseif($token->test(Token::NAME) && substr($value, 0, 1) == '$')
		{
			$subVariable = new SubVariable($this->stream, $this->environment);
			$this->elements[] = $subVariable->compile();

			$token = $this->stream->getCurrent();

			/*
			 * @todo check for duplicate code
			 */
			// next token needs to be "." OR BLOCK_END
			if(!$token->test(Token::PUNCTUATION, '.') && !$token->test(Token::BLOCK_END))
			{
				throw new SyntaxError(
					sprintf(
						'Unexpected "%s" with value "%s"',
						Token::typeToString($token->getType()),
						$token->getValue()
					),
					$token->getLine(),
					$this->stream->getFilename()
				);
			}

			elseif($token->test(Token::PUNCTUATION, '.'))
			{
				$this->stream->next();
				$this->process();
			}
		}
	}
}
