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
use spoon\template\extension\Core;

require_once realpath(dirname(__FILE__) . '/../../') . '/autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

class ExtensionCoreTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Core
	 */
	private $core;

	protected function setUp()
	{
		parent::setUp();
		Autoloader::register();
		$this->core = new Core();
	}

	protected function tearDown()
	{
		$this->core = null;
		parent::tearDown();
	}

	public function testStripCode()
	{
		$input = '<?php echo myFunction(); ?>';
		$this->assertEquals('', $this->core->stripCode($input));

		$input = '<? echo "No short opening tags mister!"; ?>';
		$this->assertEquals('', $this->core->stripCode($input));
	}

	public function testStripComments()
	{
		$input = '{* comments should be stripped *}';
		$this->assertEquals('', $this->core->stripComments($input));

		$input = '{* nested {* comment *} *}';
		$this->assertEquals('', $this->core->stripComments($input));
	}
}
