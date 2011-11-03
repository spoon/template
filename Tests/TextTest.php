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
use Spoon\Template\TokenStream;
use Spoon\Template\Token;
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
	 * @var Spoon\Template\Writer
	 */
	protected $writer;

	public function setUp()
	{
		Autoloader::register();
		$this->environment = new Environment();
		$this->writer = new Writer();
	}

	public function tearDown()
	{
		$this->environment = null;
		$this->writer = null;
	}

	public function testBasic()
	{
		$expected = "// line 1\necho 'This is the content of my template file';\n";
		$stream = new TokenStream(
			array(
				new Token(Token::STRING, 'This is the content of my template file', 1),
				new Token(Token::EOF, null, 1)
			)
		);

		$text = new Text($stream, $this->environment);
		$text->compile($this->writer);
		$this->assertEquals($expected, $this->writer->getSource());
	}
}
