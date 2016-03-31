<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Apc class.
 *
 * @since  1.0
 */
class ApcTest extends CacheTest
{
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
		$this->assertInstanceOf('\Psr\Cache\CacheItemInterface', $this->instance->getItem('foo'), 'Checking getItem.');
		$this->assertInternalType('array', $this->instance->getItems(array('foo')), 'Checking getItems.');
		$this->assertInternalType('boolean', $this->instance->deleteItem('foo'), 'Checking deleteItem.');
		$this->assertInternalType('array', $this->instance->deleteItems(array('foo')), 'Checking deleteItems.');

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertInternalType('boolean', $this->instance->save($stub), 'Checking save.');
	}

	/**
	 * Tests the Joomla\Cache\Apc::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apc::clear
	 * @since   1.0
	 */
	public function testClear()
	{
		$this->assertTrue($this->instance->clear());
	}

	/**
	 * Tests the Joomla\Cache\Apc::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apc::hasItem
	 * @since   1.0
	 */
	public function testHasItem()
	{
		$this->assertTrue($this->instance->hasItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Apc::get method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apc::get
	 * @since   1.0
	 */
	public function testGet()
	{
		$this->assertInstanceOf(
			'\Psr\Cache\CacheItemInterface',
			$this->instance->getItem('foo')
		);
	}

	/**
	 * Tests the Joomla\Cache\Apc::remove method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apc::remove
	 * @since   1.0
	 */
	public function testRemove()
	{
		$this->assertTrue($this->instance->deleteItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Apc::save method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apc::save
	 * @since   1.0
	 */
	public function testSave()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('car');

		$stub->method('getKey')
			->willReturn('boo');

		$this->assertTrue($this->instance->save($stub));
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
		$this->cacheClass = 'Joomla\\Cache\\Apc';

		try
		{
			parent::setUp();

			// Create a stub for the CacheItemInterface class.
			$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
				->getMock();

			$stub->method('get')
				->willReturn('bar');

			$stub->method('getKey')
				->willReturn('foo');

			$this->instance->save($stub);
		}
		catch (\Exception $e)
		{
			$this->markTestSkipped();
		}
	}
}
