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
class IfNode
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
	 * @param Spoon\Template\TokenStream
	 * @param Spoon\Template\Environment
	 */
	public function __construct(TokenStream $stream, Environment $environment)
	{
		$this->stream = $stream;
		$this->environment = $environment;
	}

	/**
	 * Writes the compiled PHP code to the writer object.
	 *
	 * @param Spoon\Template\Writer $writer
	 */
	public function compile(Writer $writer)
	{
		$token = $this->stream->next();
		$line = $token->getLine();

		$this->stream->expect(Token::NAME);

		// not a valid name (needs to start with "$")
		if(substr($token->getValue(), 0, 1) != '$')
		{
			throw new SyntaxError(
				'Expecting variable', $line, $this->stream->getFilename()
			);
		}

		$output = 'if(';
		$subVariable = new SubVariable($this->stream, $this->environment);
		$output .= $subVariable->compile() . '):' . "\n";

		$writer->write($output, $line);
		$writer->indent();
	}
}
