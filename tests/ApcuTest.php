<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Apcu class.
 *
 * @since  1.0
 */
class ApcuTest extends CacheTest
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
		$this->assertInternalType('boolean', $this->instance->deleteItems(array('foo')), 'Checking deleteItems.');

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
	 * Tests the Joomla\Cache\Apcu::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apcu::clear
	 * @since   1.0
	 */
	public function testClear()
	{
		$this->assertTrue($this->instance->clear());
	}

	/**
	 * Tests the Joomla\Cache\Apcu::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apcu::hasItem
	 * @since   1.0
	 */
	public function testHasItem()
	{
		$this->assertTrue($this->instance->hasItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Apcu::getItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apcu::getItem
	 * @since   1.0
	 */
	public function testGetItem()
	{
		$this->assertInstanceOf(
			'\Psr\Cache\CacheItemInterface',
			$this->instance->getItem('foo')
		);
	}

	/**
	 * Tests the Joomla\Cache\Apcu::deleteItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apcu::deleteItem
	 * @since   1.0
	 */
	public function testDeleteItem()
	{
		$this->assertTrue($this->instance->deleteItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Apcu::save method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Apcu::save
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
		$this->cacheClass = 'Joomla\\Cache\\Apcu';

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
