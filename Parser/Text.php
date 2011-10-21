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
use Spoon\Template\TokenStream;
use Spoon\Template\Environment;

/**
 * Class used to convert text tokens into PHP code.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Text
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
	 * Compiles the text token and returns its PHP code.
	 *
	 * @return string
	 */
	public function compile()
	{
		$string = str_replace('\\"', '"', addslashes($this->stream->getCurrent()->getValue()));
		return "echo '" . $string . "';\n";
	}
}
