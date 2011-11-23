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
use Spoon\Template\Writer;

/**
 * Almost completely the same as the if node.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class ElseIfNode extends IfNode
{
	/**
	 * Writes the compiled PHP code to the writer object.
	 *
	 * @param Spoon\Template\Writer $writer
	 */
	public function compile(Writer $writer)
	{
		$this->line = $this->stream->next()->getLine();

		$this->process();
		$this->cleanup();

		if($this->brackets != 0)
		{
			throw new SyntaxError(
				'Not all opened brackets were properly closed.',
				$this->line,
				$this->stream->getFilename()
			);
		}

		$writer->outdent();
		$writer->write('elseif(' . $this->output . "):\n");
		$writer->indent();
	}
}
