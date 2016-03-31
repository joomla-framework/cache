<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\XCache class.
 *
 * @since  1.0
 */
class XCacheTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Cache\XCache
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
		$this->assertInternalType('boolean', $this->instance->deleteItems(array('foo')), 'Checking removeMultiple.');

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertInternalType('boolean', $this->instance->save($stub), 'Checking set.');
	}

	/**
	 * Tests the Joomla\Cache\XCache::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\XCache::clear
	 * @since   1.0
	 */
	public function testClear()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\XCache::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\XCache::hasItem
	 * @since   1.0
	 */
	public function testHasItem()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\XCache::getItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\XCache::getItem
	 * @since   1.0
	 */
	public function testGetItem()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\XCache::deleteItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\XCache::deleteItem
	 * @since   1.0
	 */
	public function testDeleteItem()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Tests the Joomla\Cache\XCache::set method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\XCache::set
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
		if (!Cache\XCache::isSupported())
		{
			$this->markTestSkipped('XCache Cache Handler is not supported on this system.');
		}

		parent::setUp();

		$this->instance = new Cache\XCache;
	}
}
