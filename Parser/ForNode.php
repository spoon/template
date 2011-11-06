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
 * Class used to convert tokens concerning for loops into PHP code.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class ForNode
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

		// name expected, will throw an exception
		if($this->stream->expect(Token::NAME))
		{
			// only very basic variables are allowed
			if(substr($token->getValue(), 0, 1) != '$')
			{
				throw new SyntaxError(
					'Unrecognized "for" syntax. Expected something like {for $user in $users}...',
					$token->getLine(),
					$this->stream->getFilename()
				);
			}
		}

		$name = substr($token->getValue(), 1);
		$token = $this->stream->next();

		// 'in' expected
		// @todo fix custom message
		$this->stream->expect(Token::NAME, 'in', 'custom message..');
		$token = $this->stream->next();

		// expected name
		// @todo fix custom message
		$this->stream->expect(Token::NAME, null, 'custom message about for loop');

		// @todo name sucks
		$subVariable = new SubVariable($this->stream, $this->environment);
		$something = $subVariable->compile();

		/*
		 * @todo we should actually do something like:
		 * foreach($something as $key => $value)
		 *
		 * That is in the case we want to actually be able to use the keys of course. What
		 * about looping objects? Won't that be crappy coco?
		 */

		// backup current context
		$writer->write('$context[\'_parent\'] = $context;' . "\n");

		// @todo comment on this
		$writer->write('$context[\'loop\'][\'count\'] = count(' . $something . ');' . "\n");
		$writer->write('$context[\'loop\'][\'first\'] = true;' . "\n");
		$writer->write('$context[\'loop\'][\'last\'] = false;' . "\n");
		$writer->write('$context[\'loop\'][\'i\'] = 1;' . "\n");
		$writer->write('foreach(' . $something .' as $context[\'loop\'][\'key\'] => $context[\'' . $name . '\']):' . "\n");
		$writer->indent();
		$writer->write('if($context[\'loop\'][\'i\'] == $context[\'loop\'][\'count\']):' . "\n");
		$writer->indent();
		$writer->write('$context[\'loop\'][\'last\'] = true;' . "\n");
		$writer->outdent();
		$writer->write('endif;');
	}
}
