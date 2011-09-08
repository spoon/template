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
use Spoon\Template\Autoloader;
use Spoon\Template\Environment;
use Spoon\Template\Lexer;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class LexerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Lexer
	 */
	protected $lexer;

	protected function setUp()
	{
		parent::setUp();
		Autoloader::register();
		$this->lexer = new Lexer(new Environment());
	}

	protected function tearDown()
	{
		$this->lexer = null;
		parent::tearDown();
	}

	/**
	 * @expectedException Exception
	 */
	public function testException()
	{
		throw new Exception('kaka'); // @todo remove me
	}

	// @todo test possible blocks, vars and comments
}
