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
class Text
{
	/**
	 * Environment.
	 *
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	/**
	 * Token stream.
	 *
	 * @var Spoon\Template\TokenStream
	 */
	protected $stream;

	public function __construct(TokenStream $stream, Environment $environment)
	{
		$this->stream = $stream;
		$this->environment = $environment;
	}

	public function compile()
	{
		$string = addslashes($this->stream->getCurrent()->getValue());
		$string = "'" . $string . "'";
		$string = 'echo ' . $string . ";\n";
		return $string;
	}
}
