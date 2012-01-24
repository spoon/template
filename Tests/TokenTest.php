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
use Spoon\Template\Token;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TokenTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Token
	 */
	protected $token;

	protected function setUp()
	{
		Autoloader::register();
		$this->token = new Token(Token::EOF, null, 1);
	}

	protected function tearDown()
	{
		$this->token = null;
	}

	public function testToString()
	{
		$expected = '[line]: ' . $this->token->getLine() . "\n";
		$expected .= '[type]: ' . Token::typeToString($this->token->getType()) . "\n";
		$expected .= '[value]: ' . $this->token->getValue() . "\n";

		$this->assertEquals($expected, (string) $this->token);
	}

	public function testGetLine()
	{
		$this->assertEquals(1, $this->token->getLine());
	}

	public function testGetType()
	{
		$this->assertEquals(Token::EOF, $this->token->getType());
	}

	public function testGetValue()
	{
		$this->assertEquals(null, $this->token->getValue());
	}

	public function testTest()
	{
		// different token type
		$token = new Token(Token::NAME, '$blub', 3);
		$this->assertFalse($token->test(Token::BLOCK_END));

		// no values
		$this->assertTrue($token->test(Token::NAME));

		// no valid values
		$this->assertFalse($token->test(Token::NAME, array('one', 'two', 'three')));

		// valid value
		$this->assertTrue($token->test(Token::NAME, array('$blub', '$bleb', '$blab')));
	}

	public function testTypeToString()
	{
		$this->assertEquals('EOF', Token::typeToString(-1));
		$this->assertEquals('TEXT', Token::typeToString(0));
		$this->assertEquals('BLOCK_START', Token::typeToString(1));
		$this->assertEquals('BLOCK_END', Token::typeToString(2));
		$this->assertEquals('VAR_START', Token::typeToString(3));
		$this->assertEquals('VAR_END', Token::typeToString(4));
		$this->assertEquals('NAME', Token::typeToString(5));
		$this->assertEquals('NUMBER', Token::typeToString(6));
		$this->assertEquals('STRING', Token::typeToString(7));
		$this->assertEquals('OPERATOR', Token::typeToString(8));
		$this->assertEquals('PUNCTUATION', Token::typeToString(9));
	}

	/**
	 * @expectedException Spoon\Template\Exception
	 */
	public function testFailTypeToString()
	{
		Token::typeToString(1337);
	}
}
