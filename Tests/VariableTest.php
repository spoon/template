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
use Spoon\Template\Parser\Variable;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TokenTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	public function setUp()
	{
		parent::setUp();
		Autoloader::register();
		$this->environment = new Environment();
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->environment = null;
	}

	public function testBasic()
	{
		// basic variable {$foo}
		$stream = new TokenStream(
			array(
				new Token(Token::VAR_START, null, 1),
				new Token(Token::NAME, 'foo', 1),
				new Token(Token::VAR_END, null, 1),
				new Token(Token::EOF, null, 1)
			)
		);

		$expected = 'echo $this->getVar($context, array(\'foo\'));' . "\n";
		$variable = new Variable($stream, $this->environment);
		$this->assertEquals($expected, $variable->compile());
	}
}
