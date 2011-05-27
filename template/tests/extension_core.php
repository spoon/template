<?php

use spoon\template\Autoloader;
use spoon\template\Template;
use spoon\template\Extension;
use spoon\template\extension\Core;

require_once 'template/autoloader.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Core test case.
 */
class ExtensionCore extends PHPUnit_Framework_TestCase {

	/**
	 * @var Core
	 */
	private $core;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		Autoloader::register();
		$this->core = new Core();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
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
