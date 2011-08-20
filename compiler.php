<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace spoon\template;

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
	 * @var Template
	 */
	protected $template;

	/**
	 * Constructor.
	 *
	 * @param Template $template The template object.
	 * @param string $filename The location of the template you wish to compile.
	 */
	public function __construct(Template $template, $filename)
	{
		$this->template = $template;
		$this->filename = (string) $filename;
	}

	/**
	 * Parses the template using the environment extensions.
	 */
	public function parse()
	{
		// load filename into string
		$this->source = file_get_contents($this->filename);

		// parse all extensions
		foreach($this->template->getEnvironment()->getExtensions() as $extension)
		{
			$this->source = $extension->parse($this->source);
		}
	}

	/**
	 * Writes the parsed template to its cache file.
	 */
	public function write()
	{
		// parse all the extensions
		$this->parse();

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
