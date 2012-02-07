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
use Spoon\Template\Template;
use Spoon\Template\Compiler;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class CompilerTest extends \PHPUnit_Framework_TestCase
{
	protected $path;

	/**
	 * @var Spoon\Template\Template
	 */
	protected $template;

	protected function setUp()
	{
		Autoloader::register();
		$this->template = new Template(
			new Environment(
				array(
					'cache' => dirname(__FILE__) . '/cache'
				)
			)
		);
		$this->path = dirname(__FILE__) . '/templates';
	}

	protected function tearDown()
	{
		$this->template = null;
	}

	public function testCompileTextToken()
	{
		$compiler = new Compiler($this->template, $this->path . '/text_token.tpl');
		$compiler->write();
		$this->assertTrue(true);
	}

	/**
	 * @expectedException Spoon\Template\SyntaxError
	 */
	public function testNonExistingTag()
	{
		$compiler = new Compiler($this->template, $this->path . '/no_such_tag.tpl');
		$compiler->write();
	}
}
