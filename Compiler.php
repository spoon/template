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
use Spoon\Template\Writer;
use Spoon\Template\Parser\Variable;
use Spoon\Template\Parser\Text;

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
	 * Compiled source.
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * Template instance.
	 *
	 * @var Spoon\Template
	 */
	protected $template;

	/**
	 * @param Spoon\Template $template The template object.
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
	 * @return string
	 */
	protected function compile()
	{
		$lexer = new Lexer($this->template->getEnvironment());
		$stream = $lexer->tokenize(file_get_contents($this->filename), basename($this->filename));

		$writer = new Writer();
		$writer->write("<?php\n");
		$writer->write("\n");
		$writer->write('namespace Spoon\Template;' . "\n");
		$writer->write("\n");
		$writer->write('/* ' . $this->filename . ' */' . "\n");

		$class = $this->template->getEnvironment()->getCacheFilename($this->filename);
		$class = substr($class, 0, -8) . '_Template';
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
					$writer->write($text->compile(), $token->getLine());
					break;

				case Token::VAR_START:
					$variable = new Variable($stream, $this->template->getEnvironment());
					$writer->write($variable->compile(), $token->getLine());
					break;
			}

			if($token->getType() !== Token::EOF)
			{
				$token = $stream->next();
			}

			else
			{
				break;
			}
		}

		$writer->outdent();
		$writer->write("}\n");
		$writer->outdent();
		$writer->write("}\n");

		$this->source = $writer->getSource();
		return $this->source;
	}

	/**
	 * Writes the parsed template to its cache file.
	 */
	public function write()
	{
		// load filename into string
		$this->source = $this->compile();

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
		if(@file_put_contents($tmpFile, $this->source) !== false)
		{
			if(@rename($tmpFile, $file))
			{
				chmod($file, 0644);
			}
		}
	}
}
