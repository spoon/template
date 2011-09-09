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
use Spoon\Template\Token;
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

	public function testVariable()
	{
		// basic variable
		$source = '{$foo}';
		$expected = array(
			new Token(Token::VAR_START, null, 1),
			new Token(Token::NAME, 'foo', 1),
			new Token(Token::VAR_END, null, 1),
			new Token(Token::EOF, null, 1)
		);
		$this->assertEquals($expected, $this->lexer->tokenize($source));

		// 2 parts
		$source = '{$foo.bar}';
		$expected = array(
			new Token(Token::VAR_START, null, 1),
			new Token(Token::NAME, 'foo', 1),
			new Token(Token::PUNCTUATION, '.', 1),
			new Token(Token::NAME, 'bar', 1),
			new Token(Token::VAR_END, null, 1),
			new Token(Token::EOF, null, 1)
		);
		$this->assertEquals($expected, $this->lexer->tokenize($source));

		// 3 parts
		$source = '{$foo.bar.baz}';
		$expected = array(
			new Token(Token::VAR_START, null, 1),
			new Token(Token::NAME, 'foo', 1),
			new Token(Token::PUNCTUATION, '.', 1),
			new Token(Token::NAME, 'bar', 1),
			new Token(Token::PUNCTUATION, '.', 1),
			new Token(Token::NAME, 'baz', 1),
			new Token(Token::VAR_END, null, 1),
			new Token(Token::EOF, null, 1)
		);
		$this->assertEquals($expected, $this->lexer->tokenize($source));
	}

	public function testVariableModifiers()
	{
		// one modifier
		$source = '{$foo|upper}';
		$expected = array(
			new Token(Token::VAR_START, null, 1),
			new Token(Token::NAME, 'foo', 1),
			new Token(Token::PUNCTUATION, '|', 1),
			new Token(Token::NAME, 'upper', 1),
			new Token(Token::VAR_END, null, 1),
			new Token(Token::EOF, null, 1)
		);
		$this->assertEquals($expected, $this->lexer->tokenize($source));

		// chained modifiers
		$source = '{$foo|upper|lower}';
		$expected = array(
			new Token(Token::VAR_START, null, 1),
			new Token(Token::NAME, 'foo', 1),
			new Token(Token::PUNCTUATION, '|', 1),
			new Token(Token::NAME, 'upper', 1),
			new Token(Token::PUNCTUATION, '|', 1),
			new Token(Token::NAME, 'lower', 1),
			new Token(Token::VAR_END, null, 1),
			new Token(Token::EOF, null, 1)
		);
		$this->assertEquals($expected, $this->lexer->tokenize($source));

		// modifier with arguments
		$source = '{$foo|rand(1,2)}';
		$expected = array(
			new Token(Token::VAR_START, null, 1),
			new Token(Token::NAME, 'foo', 1),
			new Token(Token::PUNCTUATION, '|', 1),
			new Token(Token::NAME, 'rand', 1),
			new Token(Token::PUNCTUATION, '(', 1),
			new Token(Token::NUMBER, 1, 1),
			new Token(Token::PUNCTUATION, ',', 1),
			new Token(Token::NUMBER, 2, 1),
			new Token(Token::PUNCTUATION, ')', 1),
			new Token(Token::VAR_END, null, 1),
			new Token(Token::EOF, null, 1)
		);
		$this->assertEquals($expected, $this->lexer->tokenize($source));

		// add some extra spaces to the arguments
		$source = '{$foo|rand( 1 , 2 )}';
		$this->assertEquals($expected, $this->lexer->tokenize($source));
	}

	public function testAdvancedVariables()
	{
		$source = '{$foo.bar(1).baz|upper|custom("var")}';
		$expected = array(
			new Token(Token::VAR_START, null, 1),
			new Token(Token::NAME, 'foo', 1),
			new Token(Token::PUNCTUATION, '.', 1),
			new Token(Token::NAME, 'bar', 1),
			new Token(Token::PUNCTUATION, '(', 1),
			new Token(Token::NUMBER, 1, 1),
			new Token(Token::PUNCTUATION, ')', 1),
			new Token(Token::PUNCTUATION, '.', 1),
			new Token(Token::NAME, 'baz', 1),
			new Token(Token::PUNCTUATION, '|', 1),
			new Token(Token::NAME, 'upper', 1),
			new Token(Token::PUNCTUATION, '|', 1),
			new Token(Token::NAME, 'custom', 1),
			new Token(Token::PUNCTUATION, '(', 1),
			new Token(Token::STRING, 'var', 1),
			new Token(Token::PUNCTUATION, ')', 1),
			new Token(Token::VAR_END, null, 1),
			new Token(Token::EOF, null, 1)
		);
		$this->assertEquals($expected, $this->lexer->tokenize($source));
	}
}
