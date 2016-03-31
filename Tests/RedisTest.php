<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Redis class.
 *
 * @since  1.0
 */
class RedisTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var	Cache\Redis
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
	 * Tests the Joomla\Cache\Redis::get and Joomla\Cache\Redis::set methods.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::getItem
	 * @covers  Joomla\Cache\Redis::save
	 * @covers  Joomla\Cache\Redis::connect
	 * @since   1.0
	 */
	public function testGetAndSave()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertTrue(
			$this->instance->save($stub),
			'Should store the data properly'
		);

		$this->assertEquals(
			'bar',
			$this->instance->getItem('foo')->get(),
			'Checking get'
		);
	}

	/**
	 * Tests the Joomla\Cache\Redis::get and Joomla\Cache\Redis::set methods with timeout
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::getItem
	 * @covers  Joomla\Cache\Redis::save
	 * @covers  Joomla\Cache\Redis::connect
	 * @since   1.0
	 */
	public function testGetAndSaveWithTimeout()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Joomla\\Cache\\Item\\AbstractItem')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$expireDate = new \DateTime;
		$expireDate->setTimestamp(time() - 1);
		$stub->method('getExpiration')
			->willReturn($expireDate);

		$this->assertTrue(
			$this->instance->save($stub),
			'Should store the data properly'
		);

		sleep(2);

		$this->assertFalse(
			$this->instance->getItem('foo')->isHit(),
			'Checks expired get.'
		);
	}

	/**
	 * Tests the Joomla\Cache\Redis::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::clear
	 * @covers  Joomla\Cache\Redis::connect
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
			->willReturn('boo');

		$this->instance->save($stub);
		$this->instance->save($stub2);

		$this->instance->clear();

		$this->assertFalse(
			$this->instance->getItem('foo')->isHit(),
			'Item should have been removed'
		);

		$this->assertFalse(
			$this->instance->getItem('goo')->isHit(),
			'Item should have been removed'
		);
	}

	/**
	 * Tests the Joomla\Cache\Redis::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::connect
	 * @covers  Joomla\Cache\Redis::hasItem
	 * @since   1.0
	 */
	public function testHasItem()
	{
		$this->assertFalse(
			$this->instance->hasItem('foo'),
			'Item should not exist'
		);

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);

		$this->assertTrue(
			$this->instance->hasItem('foo'),
			'Item should exist'
		);
	}

	/**
	 * Tests the Joomla\Cache\Redis::remove method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::connect
	 * @covers  Joomla\Cache\Redis::deleteItem
	 * @since   1.0
	 */

	public function testRemove()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertTrue(
			$this->instance->getItem('foo')->isHit(),
			'Item should exist'
		);

		$this->instance->deleteItem('foo');

		$this->assertFalse(
			$this->instance->getItem('foo')->isHit(),
			'Item should have been removed'
		);
	}

	/**
	 * Tests the Joomla\Cache\Redis::getItems method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::getItems
	 * @since   1.0
	 */
	public function testGetItems()
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
			->willReturn('bar');

		$stub2->method('getKey')
			->willReturn('boo');

		$this->instance->save($stub);
		$this->instance->save($stub2);

		$fooResult = $this->instance->getItems(array('foo', 'boo'));

		$this->assertArrayHasKey('foo', $fooResult, 'Missing array key');
		$this->assertArrayHasKey('boo', $fooResult, 'Missing array key');

		$this->assertInstanceOf(
			'Joomla\Cache\Item\Item',
			$fooResult['foo'],
			'Expected instance of Joomla\Cache\Item\Item'
		);
		$this->assertInstanceOf(
			'Joomla\Cache\Item\Item',
			$fooResult['boo'],
			'Expected instance of Joomla\Cache\Item\Item'
		);

		$this->assertTrue(
			$fooResult['foo']->isHit(),
			'Item should be returned from cache'
		);
		$this->assertTrue(
			$fooResult['boo']->isHit(),
			'Item should be returned from cache'
		);
	}

	/**
	 * Tests the Joomla\Cache\Redis::deleteItems method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::deleteItems
	 * @since   1.0
	 */
	public function testDeleteItems()
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
			->willReturn('bar');

		$stub2->method('getKey')
			->willReturn('boo');

		$this->instance->save($stub);
		$this->instance->save($stub2);

		$this->instance->deleteItems(array('foo', 'boo'));

		$this->assertFalse(
			$this->instance->getItem('foo')->isHit(),
			'Item should have been removed'
		);
		$this->assertFalse(
			$this->instance->getItem('boo')->isHit(),
			'Item should have been removed'
		);
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Redis::__construct
	 * @since   1.0
	 */
	protected function setUp()
	{
		parent::setUp();

		try
		{
			$this->instance = new Cache\Redis;
		}
		catch (\Exception $e)
		{
			$this->markTestSkipped($e->getMessage());
		}
	}

	/**
	 * Flush all data before each test
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function assertPreConditions()
	{
		if ($this->instance)
		{
			$this->instance->clear();
		}
	}

	/**
	 * Teardown the test.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function tearDown()
	{
		if ($this->instance)
		{
			$this->instance->clear();
		}
	}
}
