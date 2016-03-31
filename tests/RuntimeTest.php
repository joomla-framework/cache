<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;
use Joomla\Test\TestHelper;

/**
 * Tests for the Joomla\Cache\Runtime class.
 *
 * @since  1.0
 */
class RuntimeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Cache\Runtime
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
	 * Tests the Joomla\Cache\Runtime::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Runtime::clear
	 * @since   1.0
	 */
	public function testClear()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		// Create a stub for the CacheItemInterface class.
		$stub2 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub2->method('get')
			->willReturn('car');

		$stub2->method('getKey')
			->willReturn('goo');

		$this->instance->save($stub);
		$this->instance->save($stub2);

		$this->assertEquals(
			'bar',
			$this->instance->getItem('foo')->get(),
			'Checks first item was set.'
		);

		$this->assertEquals(
			'car',
			$this->instance->getItem('goo')->get(),
			'Checks second item was set.'
		);

		$this->instance->clear();

		$this->assertNull(
			$this->instance->getItem('foo')->get(),
			'Checks first item was cleared.'
		);

		$this->assertNull(
			$this->instance->getItem('goo')->get(),
			'Checks second item was cleared.'
		);
	}

	/**
	 * Tests the Joomla\Cache\Runtime::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Runtime::hasItem
	 * @since   1.0
	 */
	public function testHasItem()
	{
		$this->assertFalse($this->instance->hasItem('foo'));

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertTrue($this->instance->hasItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Runtime::getItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Runtime::getItem
	 * @since   1.0
	 */
	public function testGetItem()
	{
		$item = $this->instance->getItem('foo');
		$this->assertNull($item->get());
		$this->assertFalse($item->isHit());

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertEquals('bar', $this->instance->getItem('foo')->get());
	}

	/**
	 * Tests the Joomla\Cache\Runtime::deleteItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Runtime::deleteItem
	 * @since   1.0
	 */
	public function testDeleteItem()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertEquals('bar', $this->instance->getItem('foo')->get());

		$this->instance->deleteItem('foo');
		$this->assertNull($this->instance->getItem('foo')->get());
	}

	/**
	 * Tests the Joomla\Cache\Runtime::save method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Runtime::save
	 * @since   1.0
	 */
	public function testSave()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertEquals('bar', $this->instance->getItem('foo')->get());
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
			$this->instance = new Cache\Runtime;

			// Clear the internal store.
			TestHelper::setValue($this->instance, 'store', array());
		}
		catch (\Exception $e)
		{
			$this->markTestSkipped();
		}
	}
}
