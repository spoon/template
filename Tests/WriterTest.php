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
use Spoon\Template;
use Spoon\Template\Autoloader;
use Spoon\Template\Writer;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class WriterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Writer
	 */
	protected $writer;

	protected function setUp()
	{
		parent::setUp();
		Autoloader::register();
		$this->writer = new Writer();
	}

	protected function tearDown()
	{
		$this->writer = null;
		parent::tearDown();
	}

	public function testGetSource()
	{
		$this->assertEquals(null, $this->writer->getSource());
		$this->writer->write('What do I know about diamonds?');
		$this->assertEquals('What do I know about diamonds?', $this->writer->getSource());
	}

	public function testIndent()
	{
		$this->writer->indent();
		$this->writer->write("one level\n");
		$this->assertEquals("\tone level\n", $this->writer->getSource());

		$this->writer->indent();
		$this->writer->write("second level\n");
		$this->assertEquals("\tone level\n\t\tsecond level\n", $this->writer->getSource());
	}

	public function testOutdent()
	{
		$this->writer->indent(10);
		$this->writer->outdent(8);
		$this->writer->write('I like birds');
		$this->assertEquals("\t\tI like birds", $this->writer->getSource());
	}

	public function testOutdentBelowZero()
	{
		$this->writer->outdent(100);
		$this->writer->write('I like birds');
		$this->assertEquals('I like birds', $this->writer->getSource());
	}

	public function testLineNumbers()
	{
		$this->writer->write("My first class\n", 1);
		$expected = "// line 1\nMy first class\n";
		$this->assertEquals($expected, $this->writer->getSource());

		$this->writer->write("This also belongs to line 1\n", 1);
		$expected .= "This also belongs to line 1\n";
		$this->assertEquals($expected, $this->writer->getSource());

		$this->writer->write("\n");
		$this->writer->write("Number 2, here we come!", 2);
		$expected .= "\n// line 2\nNumber 2, here we come!";
		$this->assertEquals($expected, $this->writer->getSource());
	}
}
