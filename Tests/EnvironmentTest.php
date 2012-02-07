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

require_once realpath(dirname(__FILE__) . '/../') . '/Autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Spoon\Template\Environment
	 */
	private $environment;

	protected function setUp()
	{
		Autoloader::register();
		$this->environment = new Environment();
	}

	protected function tearDown()
	{
		$this->environment = null;
		parent::tearDown();
	}

	public function testAddModifier()
	{
		$this->environment->addModifier('a', 'dump');
		$this->environment->addModifier('a_', 'dump');
		$this->environment->addModifier('a9', 'dump');
		$this->environment->addModifier('a_9', 'dump');
		$this->environment->addModifier('a9_', 'dump');
		$this->environment->addModifier('a9_', 'dump');
		$this->assertSame($this->environment, $this->environment->addModifier('a', 'dump'));
	}

	/**
	 * @expectedException Spoon\Template\Exception
	 */
	public function testAddModifierFailure()
	{
		$this->environment->addModifier('9', 'dump');
		$this->environment->addModifier('_', 'dump');
		$this->environment->addModifier('du mp', 'dump');
	}

	public function testAddTag()
	{
		$this->assertSame(
			$this->environment,
			$this->environment->addTag('foreach', 'Spoon\Template\Parser\ForNode')
		);
	}

	/**
	 * @expectedException Spoon\Template\Exception
	 */
	public function testAddTagFailureClass()
	{
		$this->environment->addTag('if', 'Spoon\Template\Environment');
	}

	/**
	 * @expectedException Spoon\Template\Exception
	 */
	public function testAddTagFailureSyntax()
	{
		$this->environment->addTag('foo bar', 'Spoon\Template\Parser\ForNode');
	}

	public function testDisableAutoEscape()
	{
		$this->environment->disableAutoEscape();
		$this->assertFalse($this->environment->isAutoEscape());
		$this->assertSame($this->environment, $this->environment->disableAutoEscape());
	}

	public function testDisableAutoReload()
	{
		$this->environment->disableAutoReload();
		$this->assertFalse($this->environment->isAutoReload());
		$this->assertSame($this->environment, $this->environment->disableAutoReload());
	}

	public function testDisableDebug()
	{
		$this->environment->disableDebug();
		$this->assertFalse($this->environment->isDebug());
		$this->assertSame($this->environment, $this->environment->disableDebug());
	}

	public function testEnableAutoEscape()
	{
		$this->environment->enableAutoEscape();
		$this->assertTrue($this->environment->isAutoEscape());
		$this->assertSame($this->environment, $this->environment->enableAutoEscape());
	}

	public function testEnableAutoReload()
	{
		$this->environment->enableAutoReload();
		$this->assertTrue($this->environment->isAutoReload());
		$this->assertSame($this->environment, $this->environment->enableAutoReload());
	}

	public function testEnableDebug()
	{
		$this->environment->enableDebug();
		$this->assertTrue($this->environment->isDebug());
		$this->assertSame($this->environment, $this->environment->enableDebug());
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

	public function testGetModifier()
	{
		$this->environment->addModifier('dump', 'dump');
		$this->assertEquals('dump', $this->environment->getModifier('dump'));

		$this->environment->addModifier('dump', array('Debug', 'dump'));
		$this->assertEquals(array('Debug', 'dump'), $this->environment->getModifier('dump'));

		$this->environment->addModifier('dump', 'Debug::dump');
		$this->assertEquals('Debug::dump', $this->environment->getModifier('dump'));
	}

	/**
	 * @expectedException Spoon\Template\Exception
	 */
	public function testGetModifierFailure()
	{
		$this->environment->getModifier('doesNotExist');
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

	public function testRemoveModifier()
	{
		$expected = $this->environment->getModifiers();
		$this->environment->addModifier('custom', 'custom_function');
		$this->environment->removeModifier('custom');
		$this->assertEquals($expected, $this->environment->getModifiers());

		$this->environment->addModifier('custom', 'custom_function');
		$this->assertSame($this->environment, $this->environment->removeModifier('custom'));
	}

	public function testRemoveTag()
	{
		$expected = $this->environment->getTags();
		$this->environment->addTag('blub', 'Spoon\Template\Parser\IfNode');
		$this->environment->removeTag('blub');
		$this->assertEquals($expected, $this->environment->getTags());

		$this->environment->addTag('blub', 'Spoon\Template\Parser\IfNode');
		$this->assertSame($this->environment, $this->environment->removeTag('blug'));
	}

	public function testSetCache()
	{
		$this->environment->setCache(__DIR__);
		$this->assertEquals(__DIR__, $this->environment->getCache());
		$this->assertSame($this->environment, $this->environment->setCache('.'));
	}

	public function testSetCharset()
	{
		$this->environment->setCharset('iso-8859-1');
		$this->assertEquals('iso-8859-1', $this->environment->getCharset());
		$this->assertSame($this->environment, $this->environment->setCharset('utf-8'));
	}
}
