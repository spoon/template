<?php

use spoon\template\Component;

require_once 'template/component.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Component test case.
 */
class ComponentTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Component
	 */
	private $component;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp ();
		$this->component = new Component();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->component = null;
		parent::tearDown ();
	}

	/*public function testAddExtension()
	{
		// @todo not yet implemented
	}*/

	public function testDisableAutoEscape()
	{
		$this->component->disableAutoEscape();
		$this->assertFalse($this->component->isAutoEscape());
	}

	public function testDisableAutoReload()
	{
		$this->component->disableAutoReload();
		$this->assertFalse($this->component->isAutoReload());
	}

	public function testDisableDebug()
	{
		$this->component->disableDebug();
		$this->assertFalse($this->component->isDebug());
	}

	public function testDisableStrict()
	{
		$this->component->disableStrict();
		$this->assertFalse($this->component->isStrict());
	}

	public function testEnableAutoEscape()
	{
		$this->component->enableAutoEscape();
		$this->assertTrue($this->component->isAutoEscape());
	}

	public function testEnableAutoReload()
	{
		$this->component->enableAutoReload();
		$this->assertTrue($this->component->isAutoReload());
	}

	public function testEnableDebug()
	{
		$this->component->enableDebug();
		$this->assertTrue($this->component->isDebug());
	}

	public function testEnableStrict()
	{
		$this->component->enableStrict();
		$this->assertTrue($this->component->isStrict());
	}

	public function testGetCache()
	{
		$this->assertEquals('.', $this->component->getCache());
	}

	public function testGetCharset()
	{
		$this->assertEquals('utf-8', $this->component->getCharset());
	}

	/*public function testGetExtension()
	{
		// @todo not yet implemented
	}*/

	public function testIsAutoEscape()
	{
		$this->assertTrue($this->component->isAutoEscape());
	}

	public function testIsAutoReload()
	{
		$this->assertTrue($this->component->isAutoReload());
	}

	public function testIsDebug()
	{
		$this->assertFalse($this->component->isDebug());
	}

	public function testIsStrict()
	{
		$this->assertFalse($this->component->isStrict());
	}

	/*public function testRemoveExtension()
	{
		// @todo not yet implemented
	}*/

	public function testSetCache()
	{
		$this->component->setCache(__DIR__);
		$this->assertEquals(__DIR__, $this->component->getCache());
	}

	public function testSetCharset()
	{
		$this->component->setCharset('iso-8859-1');
		$this->assertEquals('iso-8859-1', $this->component->getCharset());
	}

	/*public function testSetExtensions()
	{
		// @todo not yet implemlented
	}*/
}
