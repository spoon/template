<?php

/*
* This file is part of Spoon Library.
*
* (c) Davy Hellemans <davy@spoon-library.com>
*
* For the full copyright and license information, please view the license
* file that was distributed with this source code.
*/

use spoon\template\Autoloader;
use spoon\template\Environment;
use spoon\template\Extension;

require_once realpath(dirname(__FILE__) . '/../') . '/autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class EnvironmentTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Environment
	 */
	private $environment;

	protected function setUp()
	{
		parent::setUp();
		Autoloader::register();
		$this->environment = new Environment();
	}

	protected function tearDown()
	{
		$this->environment = null;
		parent::tearDown();
	}

	public function testDisableAutoEscape()
	{
		$this->environment->disableAutoEscape();
		$this->assertFalse($this->environment->isAutoEscape());
	}

	public function testDisableAutoReload()
	{
		$this->environment->disableAutoReload();
		$this->assertFalse($this->environment->isAutoReload());
	}

	public function testDisableDebug()
	{
		$this->environment->disableDebug();
		$this->assertFalse($this->environment->isDebug());
	}

	public function testEnableAutoEscape()
	{
		$this->environment->enableAutoEscape();
		$this->assertTrue($this->environment->isAutoEscape());
	}

	public function testEnableAutoReload()
	{
		$this->environment->enableAutoReload();
		$this->assertTrue($this->environment->isAutoReload());
	}

	public function testEnableDebug()
	{
		$this->environment->enableDebug();
		$this->assertTrue($this->environment->isDebug());
	}

	public function testGetCache()
	{
		$this->assertEquals('.', $this->environment->getCache());
	}

	public function testGetCacheFilename()
	{
		$expected = $this->environment->getCacheFilename('party.tpl');
		$result = $this->environment->getCacheFilename('../tests/party.tpl');
		$this->assertEquals($expected, $result);
	}

	public function testGetCharset()
	{
		$this->assertEquals('utf-8', $this->environment->getCharset());
	}

	public function testIsAutoEscape()
	{
		$this->assertTrue($this->environment->isAutoEscape());
	}

	public function testIsAutoReload()
	{
		$this->assertTrue($this->environment->isAutoReload());
	}

	public function testIsDebug()
	{
		$this->assertFalse($this->environment->isDebug());
	}

	public function testIsChanged()
	{
		$this->assertFalse($this->environment->isChanged(__FILE__, time()));
	}

	public function testSetCache()
	{
		$this->environment->setCache(__DIR__);
		$this->assertEquals(__DIR__, $this->environment->getCache());
	}

	public function testSetCharset()
	{
		$this->environment->setCharset('iso-8859-1');
		$this->assertEquals('iso-8859-1', $this->environment->getCharset());
	}
}
