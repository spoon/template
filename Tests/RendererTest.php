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
use Spoon\Template\Renderer;
use Spoon\Template\Environment;
use Spoon\Template\Template;

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class RendererTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Environment
	 */
	protected $environment;

	/**
	 * @var array
	 */
	protected $context = array();

	/**
	 * @var Spoon\Template\Renderer
	 */
	protected  $renderer;

	protected function setUp()
	{
		Autoloader::register();
		$this->environment = new Environment();
		$this->renderer = new Renderer($this->environment);

		// strings
		$this->context['name'] = 'Davy Hellemans';
		$this->context['email'] = 'davy@spoon-library.com';
		$this->context['message'] = '<p><a href="http://www.spoon-library.com">Spoon</a></p>';

		// arrays
		$this->context['users'] = array(
			array(
				'name' => 'Tijs Verkoyen',
				'email' => 'tijs@spoon-library.com',
				'hobbies' => array('Development')
			),
			array(
				'name' => 'Dave Lens',
				'email' => 'dave@spoon-library.com',
				'hobbies' => array('Music', 'Films', 'Development')
			),
			array(
				'name' => 'Matthias Mullie',
				'email' => 'matthias@spoon-library.com',
				'hobbies' => array('Masturbation')
			)
		);

		// objects
		$template = new Template($this->environment);
		$this->context['template'] = $template;
	}

	protected function tearDown()
	{
		$this->renderer = null;
	}

	public function testGetVar()
	{
		// strings
		$this->assertEquals(
			$this->context['name'],
			$this->renderer->getVar($this->context, array('name'))
		);
		$this->assertEquals(
			$this->context['message'],
			$this->renderer->getVar($this->context, array('message'))
		);

		// arrays
		$this->assertEquals(
			$this->context['users'][1]['name'],
			$this->renderer->getVar($this->context, array('users', 1, 'name'))
		);
		$this->assertEquals(
			$this->context['users'][2]['hobbies'][0],
			$this->renderer->getVar($this->context, array('users', 2, 'hobbies', 0))
		);

		// objects
		$this->assertEquals(
			$this->environment->isAutoEscape(),
			$this->renderer->getVar($this->context, array('template', 'getEnvironment', 'isAutoEscape'))
		);
		$this->assertEquals(
			$this->environment->isAutoEscape(),
			$this->renderer->getVar($this->context, array('template', 'environment', 'isAutoEscape'))
		);
	}
}
