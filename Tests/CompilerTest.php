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
use Spoon\Template\Compiler;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Compiler
	 */
	protected $compiler;

	protected function setUp()
	{
		Autoloader::register();
	}

	protected function tearDown()
	{
		$this->compiler = null;
	}

	public function testWrite()
	{
	}
}
