<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

namespace Spoon\Template\Tests;
use Spoon\Template\Autoloader;
use Spoon\Template\Environment;
use Spoon\Template\Lexer;
use Spoon\Template\Parser\Text;
use Spoon\Template\Writer;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TextTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	/**
	 * @var Spoon\Template\Lexer
	 */
	protected $lexer;

	/**
	 * @var Spoon\Template\Writer
	 */
	protected $writer;

	public function setUp()
	{
		Autoloader::register();
		$this->environment = new Environment();
		$this->lexer = new Lexer($this->environment);
		$this->writer = new Writer();
	}

	public function tearDown()
	{
		$this->environment = null;
		$this->lexer = null;
		$this->writer = null;
	}

	public function testBasic()
	{
		$source = 'This is the content of my template file';
		$expected = "// line 1\necho '" . $source . "';\n";

		$text = new Text($this->lexer->tokenize($source), $this->environment);
		$text->compile($this->writer);

		$this->assertEquals($expected, $this->writer->getSource());
	}
}
