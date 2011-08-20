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
use spoon\template\Template;
use spoon\template\Environment;

require_once realpath(dirname(__FILE__) . '/../') . '/autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class TemplateTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Template
	 */
	private $template;

	protected function setUp()
	{
		parent::setUp();
		Autoloader::register();
		$this->template = new Template(new Environment());
	}

	protected function tearDown()
	{
		$this->template = null;
		parent::tearDown();
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
	}

	public function  testGet()
	{
		$this->template->assign('name', 'Davy Hellemans');
		$this->assertEquals('Davy Hellemans', $this->template->get('name'));
	}

	public function testRemove()
	{
		$this->template->assign('name', 'Davy Hellemans');
		$this->template->remove('name');
		$this->assertEquals(null, $this->template->get('name'));
	}
}
