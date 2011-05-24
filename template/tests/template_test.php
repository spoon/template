<?php

use spoon\template\Template;
use spoon\template\Extension;

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

	public function testAddExtension()
	{
		// @todo not yet implemented
	}

	public function testAssign()
	{
		// strings
		$this->template->assign('key', 'value');
		$this->assertEquals('value', $this->template->get('key'));

		// arrays
		$this->template->assign(array('foo' => 'bar'));
		$this->assertEquals('bar', $this->template->get('foo'));

		// objects
		$object = new stdClass();
		$object->name = 'Template';
		$object->email = 'template@spoon-library.com';
		$this->template->assign($object);
		$this->assertEquals($object->email, $this->template->get('email'));
		$this->template->assign('person', $object);
		$this->assertEquals($object, $this->template->get('person'));

		// @todo add more checks?
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

	public function  testGet()
	{
		$this->template->assign('name', 'Davy Hellemans');
		$this->assertEquals('Davy Hellemans', $this->template->get('name'));
	}

	public function testGetCache()
	{
		$this->assertEquals('.', $this->template->getCache());
	}

	public function testGetCacheFilename()
	{
		$expected = $this->template->getCacheFilename('party.tpl');
		$result = $this->template->getCacheFilename('../tests/party.tpl');
		$this->assertEquals($expected, $result);
	}

	public function testGetCharset()
	{
		$this->assertEquals('utf-8', $this->template->getCharset());
	}

	public function testGetExtension()
	{
		// @todo not yet implemented
	}

	public function testGetExtensions()
	{
		// @todo not yet implemented
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

	public function testIsChanged()
	{
		$this->assertFalse($this->template->isChanged(__FILE__, time() - 10));
	}

	public function testIsStrict()
	{
		$this->assertFalse($this->template->isStrict());
	}

	public function testRemove()
	{
		$this->template->assign('name', 'Davy Hellemans');
		$this->template->remove('name');
		$this->assertEquals(null, $this->template->get('name'));
	}

	public function testRemoveExtension()
	{
		// @todo not yet implemented
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

	public function testSetExtensions()
	{
		// @todo not yet implemented
	}
}
