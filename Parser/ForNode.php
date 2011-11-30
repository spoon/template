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
use Spoon\Template\Writer;

/**
 * Class used to convert tokens concerning for loops into PHP code.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class ForNode extends Node
{
	/**
	 * Writes the compiled PHP code to the writer object.
	 *
	 * @param Spoon\Template\Writer $writer
	 */
	public function compile(Writer $writer)
	{
		$token = $this->stream->next();

		// name expected (starting with '$')
		if($this->stream->expect(Token::NAME))
		{
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
		$this->stream->expect(Token::NAME, 'in');
		$token = $this->stream->next();

		// name expected
		$this->stream->expect(Token::NAME);

		// @todo name sucks
		$subVariable = new SubVariable($this->stream, $this->environment);
		$something = $subVariable->compile();

		// backup current context
		$writer->write('$context[\'_parent\'] = $context;' . "\n");

		// @todo comment on this
		$writer->write('$context[\'loop\'][\'count\'] = count(' . $something . ');' . "\n");
		$writer->write('$context[\'loop\'][\'first\'] = true;' . "\n");
		$writer->write('$context[\'loop\'][\'last\'] = false;' . "\n");
		$writer->write('$context[\'loop\'][\'index\'] = 1;' . "\n");
		$writer->write('foreach(' . $something .' as $context[\'loop\'][\'key\'] => $context[\'' . $name . '\']):' . "\n");
		$writer->indent();
		$writer->write('if($context[\'loop\'][\'index\'] == $context[\'loop\'][\'count\']):' . "\n");
		$writer->indent();
		$writer->write('$context[\'loop\'][\'last\'] = true;' . "\n");
		$writer->outdent();
		$writer->write('endif;');
	}
}
