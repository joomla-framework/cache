<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Wincache class.
 *
 * @since  1.0
 */
class WincacheTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Cache\Wincache
	 * @since  1.0
	 */
	private $instance;

	/**
	 * Tests for the correct Psr\Cache return values.
	 *
	 * @return  void
	 *
	 * @coversNothing
	 * @since   1.0
	 */
	public function testPsrCache()
	{
		$this->assertInternalType('boolean', $this->instance->clear(), 'Checking clear.');
		$this->assertInstanceOf('\Psr\Cache\CacheItemInterface', $this->instance->getItem('foo'), 'Checking get.');
		$this->assertInternalType('array', $this->instance->getItems(array('foo')), 'Checking getMultiple.');
		$this->assertInternalType('boolean', $this->instance->deleteItem('foo'), 'Checking remove.');
		$this->assertInternalType('array', $this->instance->deleteItems(array('foo')), 'Checking removeMultiple.');
		$this->assertInternalType('boolean', $this->instance->set('for', 'bar'), 'Checking set.');
		$this->assertInternalType('boolean', $this->instance->setMultiple(array('foo' => 'bar')), 'Checking setMultiple.');
	}

	/**
	 * Tests the Joomla\Cache\Wincache::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::clear
	 * @since   1.0
	 */
	public function testClear()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\Wincache::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::hasItem
	 * @since   1.0
	 */
	public function testHasItem()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\Wincache::getItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::getItem
	 * @since   1.0
	 */
	public function testGetItem()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\Wincache::deleteItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::deleteItem
	 * @since   1.0
	 */
	public function testDeleteItem()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\Wincache::set method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::set
	 * @since   1.0
	 */
	public function testSet()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		try
		{
			$this->instance = new Cache\Wincache;
		}
		catch (\Exception $e)
		{
			$this->markTestSkipped();
		}
	}
}
