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
use Spoon\Template\Parser\Text;
use Spoon\Template\Parser\Variable;
use Spoon\Template\Parser\IncludeNode;
use Spoon\Template\Writer;

/**
 * Class the compiles template files to cached php files.
 *
 * @author Davy Hellemans <davy@spoon-library.com>
 */
class Compiler
{
	/**
	 * Source file to compile.
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * Template instance.
	 *
	 * @var Spoon\Template\Template
	 */
	protected $template;

	/**
	 * @param Spoon\Template\Template $template The template object.
	 * @param string $filename The location of the template you wish to compile.
	 */
	public function __construct(Template $template, $filename)
	{
		$this->template = $template;
		$this->filename = (string) $filename;
	}

	/**
	 * Compiles the template based on the tokens found in it.
	 *
	 * @todo write some inline docs to clarify the code
	 *
	 * @return string
	 */
	protected function compile()
	{
		$lexer = new Lexer($this->template->getEnvironment());
		$stream = $lexer->tokenize(file_get_contents($this->filename), basename($this->filename));

		// unique class based on the filename
		$class = $this->template->getEnvironment()->getCacheFilename($this->filename);
		$class = 'S' . substr($class, 0, -8) . '_Template';

		// writer object which contains the parsed PHP code
		$writer = new Writer();
		$writer->write("<?php\n");
		$writer->write("\n");
		$writer->write('namespace Spoon\Template;' . "\n");
		$writer->write("\n");
		$writer->write('/* ' . $this->filename . ' */' . "\n");
		$writer->write("class $class extends Template\n");
		$writer->write("{\n");
		$writer->indent();
		$writer->write('protected function display(array $context)' . "\n");
		$writer->write("{\n");
		$writer->indent();

		$token = $stream->getCurrent();
		while(!$stream->isEof())
		{
			switch($token->getType())
			{
				case Token::TEXT:
					$text = new Text($stream, $this->template->getEnvironment());
					$text->compile($writer);
					break;

				case Token::VAR_START:
					$stream->next();
					$variable = new Variable($stream, $this->template->getEnvironment());
					$variable->compile($writer);
					break;

				case Token::BLOCK_START:
					$token = $stream->next();
					switch($token->getValue())
					{
						case 'include':
							$include = new IncludeNode($stream, $this->template->getEnvironment());
							$include->compile($writer);
							break;

						default:
							throw new SyntaxError(
								sprintf('There is no such template tag "%s"', $token->getValue()),
								$token->getLine(),
								$this->filename
							);
					}
					break;
			}

			if($token->getType() !== Token::EOF)
			{
				$token = $stream->next();
			}

			else break;
		}

		$writer->outdent();
		$writer->write("}\n");
		$writer->outdent();
		$writer->write("}\n");

		return $writer->getSource();
	}

	/**
	 * Writes the parsed template to its cache file.
	 */
	public function write()
	{
		// compile template
		$source = $this->compile();

		// file location
		$file = $this->template->getEnvironment()->getCache() . '/';
		$file .= $this->template->getEnvironment()->getCacheFilename($this->filename);

		// attempt to create the directory if needed
		if(!is_dir(dirname($file)))
		{
			mkdir(dirname($file), 0777, true);
		}

		// write to tempfile and rename
		$tmpFile = tempnam(dirname($file), basename($file));
		if(@file_put_contents($tmpFile, $source) !== false)
		{
			if(@rename($tmpFile, $file))
			{
				chmod($file, 0644);
			}
		}
	}
}
