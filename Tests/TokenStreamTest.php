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
use Spoon\Template\TokenStream;
use Spoon\Template\Token;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TokenStreamTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\TokenStream
	 */
	protected $stream;

	public function setUp()
	{
		Autoloader::register();

		$template = 'Name: {$name}' . "\n";
		$template .= 'E-mail: {$email|lowercase}' . "\n\n";
		$template .= '{% if $name == $email %}chill mate{% endif %}' . "\n";

		$lexer = new Lexer(new Environment());
		$this->stream = $lexer->tokenize($template, 'imaginary-template.tpl');
	}

	public function tearDown()
	{
		$this->stream = null;
	}

	public function testExpect()
	{
		// expecting TEXT type with 'Name: ' as value
		$this->stream->expect(Token::TEXT, 'Name: ');
		$this->stream->next();

		// expecting VAR_START
		$this->stream->expect(Token::VAR_START);
		$this->stream->next();

		// expecting NAME with value '$name'
		$this->stream->expect(Token::NAME, 'name');
		$this->assertTrue(true);
	}

	/**
	 * @expectedException Spoon\Template\SyntaxError
	 */
	public function testFailExpect()
	{
		$this->stream->expect(Token::OPERATOR, '--');
	}

	public function testGetCurrent()
	{
		$token = new Token(Token::TEXT, 'Name: ', 1);
		$this->assertEquals($token, $this->stream->getCurrent());

		$token = $this->stream->next();
		$this->assertEquals($token, $this->stream->getCurrent());
	}

	public function testGetFilename()
	{
		$this->assertEquals('imaginary-template.tpl', $this->stream->getFilename());
	}

	public function testIsEof()
	{
		$this->assertFalse($this->stream->isEof());

		$stream = new TokenStream(array(new Token(Token::EOF, null, 1)));
		$this->assertTrue($stream->isEof());
	}

	public function testLook()
	{
		$token = new Token(Token::TEXT, "\nE-mail: ", 1);
		$this->assertEquals($token, $this->stream->look(4));
	}

	public function testNext()
	{
		$token = new Token(Token::VAR_START, null, 1);
		$this->assertEquals($token, $this->stream->next());

		$this->assertEquals($this->stream->look(), $this->stream->next());
	}

	public function testPrevious()
	{
		$token = $this->stream->getCurrent();
		$this->stream->next();
		$this->assertEquals($token, $this->stream->previous());
	}
}
