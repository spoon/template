<?php

use spoon\template\Template;

require_once 'template/template.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Template test case.
 */
class TemplateTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Template
	 */
	private $template;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp ();
		$this->template = new Template();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->template = null;
		parent::tearDown ();
	}

	public function testDisableAutoEscape()
	{
		$this->template->disableAutoEscape();
		$this->assertFalse($this->template->isAutoEscape());
	}

	public function testDisableAutoReload()
	{
		$this->template->disableAutoReload();
		$this->assertFalse($this->template->isAutoReload());
	}

	public function testDisableDebug()
	{
		$this->template->disableDebug();
		$this->assertFalse($this->template->isDebug());
	}

	public function testDisableStrict()
	{
		$this->template->disableStrict();
		$this->assertFalse($this->template->isStrict());
	}

	public function testEnableAutoEscape()
	{
		$this->template->enableAutoEscape();
		$this->assertTrue($this->template->isAutoEscape());
	}

	public function testEnableAutoReload()
	{
		$this->template->enableAutoReload();
		$this->assertTrue($this->template->isAutoReload());
	}

	public function testEnableDebug()
	{
		$this->template->enableDebug();
		$this->assertTrue($this->template->isDebug());
	}

	public function testEnableStrict()
	{
		$this->template->enableStrict();
		$this->assertTrue($this->template->isStrict());
	}

	public function testGetCache()
	{
		$this->assertEquals('.', $this->template->getCache());
	}

	public function testGetCharset()
	{
		$this->assertEquals('utf-8', $this->template->getCharset());
	}

	public function testIsAutoEscape()
	{
		$this->assertTrue($this->template->isAutoEscape());
	}

	public function testIsAutoReload()
	{
		$this->assertTrue($this->template->isAutoReload());
	}

	public function testIsDebug()
	{
		$this->assertFalse($this->template->isDebug());
	}

	public function testIsStrict()
	{
		$this->assertFalse($this->template->isStrict());
	}

	public function testSetCache()
	{
		$this->template->setCache(__DIR__);
		$this->assertEquals(__DIR__, $this->template->getCache());
	}

	public function testSetCharset()
	{
		$this->template->setCharset('iso-8859-1');
		$this->assertEquals('iso-8859-1', $this->template->getCharset());
	}
}
