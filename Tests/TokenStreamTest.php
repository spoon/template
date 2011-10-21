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
		parent::setUp();
		Autoloader::register();
	}

	public function tearDown()
	{
		$this->stream = null;
		parent::tearDown();
	}

	public function testExpect()
	{
	}

	public function testGetCurrent()
	{
	}

	public function testGetFilename()
	{
	}

	public function testIsEof()
	{
	}

	public function testLook()
	{
	}

	public function testNext()
	{
	}
}
