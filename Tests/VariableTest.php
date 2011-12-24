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
use Spoon\Template\Writer;
use Spoon\Template\Parser\VariableNode;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class VariableTest extends \PHPUnit_Framework_TestCase
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
		// {$foo}
		$expected = "// line 1\n";
		$expected .= 'echo $this->getVar($context, array(\'foo\'));' . "\n";
		$stream = new TokenStream(
			array(
				new Token(Token::VAR_START, null, 1),
				new Token(Token::NAME, 'foo', 1),
				new Token(Token::VAR_END, null, 1),
				new Token(Token::EOF, null, 1)
			)
		);

		// skip VAR_START
		$stream->next();

		$variable = new VariableNode($stream, $this->environment);
		$variable->compile($this->writer);
		$this->assertEquals($expected, $this->writer->getSource());
	}
}
