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

/**
 * Represents a token stream.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class TokenStream
{
	/**
	 * Current token.
	 *
	 * @var int
	 */
	protected $current = 0;

	/**
	 * The list of all tokens.
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * Constructor.
	 *
	 * @param Environment $environment
	 */
	public function __construct(array $tokens, $filename = null)
	{
		$this->tokens = $tokens;
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
}
