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
use Spoon\Template\Writer;
use Spoon\Template\TokenStream;
use Spoon\Template\Token;
use Spoon\Template\Environment;
use Spoon\Template\Parser\DebugNode;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class DebugNodeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Parser\DebugNode
	 */
	protected $node;

	/**
	 * @var Spoon\Template\Writer
	 */
	protected $writer;

	public function setUp()
	{
		Autoloader::register();
		$this->writer = new Writer();

		// {endif}
		$stream = new TokenStream(
			array(
				new Token(Token::BLOCK_START, null, 1),
				new Token(Token::NAME, 'endif', 1),
				new Token(Token::BLOCK_END, null, 1)
			)
		);
		$this->node = new DebugNode($stream, new Environment());
	}

	public function tearDown()
	{
		$this->writer = null;
		$this->node = null;
	}

	public function testCompile()
	{
		$this->node->compile($this->writer);
		$this->assertEquals(
			"// line 1\nvar_dump(\$context);\n",
			$this->writer->getSource()
		);
	}
}
