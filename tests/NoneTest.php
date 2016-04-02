<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;
use Joomla\Test\TestHelper;

/**
 * Tests for the Joomla\Cache\None class.
 */
class NoneTest extends CacheTest
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->instance = new Cache\None($this->cacheOptions);
	}

	/**
	 * Tests the Joomla\Cache\Cache::clear method.
	 */
	public function testClear()
	{
		$this->assertTrue($this->instance->clear());
	}

	/**
	 * Tests the the Joomla\Cache\Cache::getItem method.
	 */
	public function testGetItem()
	{
		$this->assertInstanceOf('Joomla\Cache\Item\Item', $this->instance->getItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Cache::save method.
	 */
	public function testSave()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('Psr\Cache\CacheItemInterface')
			->getMock();

		$this->assertTrue($this->instance->save($stub));
	}

	/**
	 * Tests the Joomla\Cache\Cache::getItems method.
	 */
	public function testGetItems()
	{
		$keys = ['foo', 'bar', 'hello'];

		$this->assertContainsOnlyInstancesOf('Psr\Cache\CacheItemInterface', $this->instance->getItems($keys));
	}

	/**
	 * Tests the Joomla\Cache\Cache::deleteItems method.
	 */
	public function testDeleteItems()
	{
		$keys = ['foo', 'bar', 'hello'];

		$this->assertTrue($this->instance->deleteItems($keys));
	}

	/**
	 * Tests the Joomla\Cache\Cache::deleteItem method.
	 */
	public function testDeleteItem()
	{
		$this->assertTrue($this->instance->deleteItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Cache::hasItem method.
	 */
	public function testHasItem()
	{
		$this->assertFalse($this->instance->hasItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Cache::commit method.
	 */
	public function testCommit()
	{
		$stubKey = 'fooCommit';
		$this->assertFalse($this->instance->hasItem($stubKey), 'Item should not exist at test start');

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('barCommit');

		$stub->method('getKey')
			->willReturn($stubKey);

		TestHelper::setValue($this->instance, 'deferred', array($stubKey => $stub));

		$this->assertTrue($this->instance->commit(), 'Commit should return boolean true as successful');

		$this->assertFalse($this->instance->hasItem($stubKey));
	}

	/**
	 * Tests for the correct Psr\Cache return values.
	 *
	 * @coversNothing
	 */
	public function testPsrCache()
	{
		$cacheInstance = $this->instance;
		$cacheClass = get_class($cacheInstance);
		$interfaces = class_implements($cacheClass);
		$psrInterface = 'Psr\\Cache\\CacheItemPoolInterface';
		$this->assertArrayHasKey($psrInterface, $interfaces, __LINE__);

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertInternalType('boolean', $cacheInstance->clear(), 'Checking clear.');
		$this->assertInternalType('boolean', $cacheInstance->save($stub), 'Checking save.');
		$this->assertNull($cacheInstance->getItem('foo')->get(), 'Checking get.');
		$this->assertInstanceOf('\Psr\Cache\CacheItemInterface', $cacheInstance->getItem('foo'), 'Checking getItem.');
		$this->assertInternalType('boolean', $cacheInstance->deleteItem('foo'), 'Checking deleteItem.');
		$this->assertInternalType('array', $cacheInstance->getItems(['foo']), 'Checking getItems.');
		$this->assertInternalType('boolean', $cacheInstance->deleteItems(['foo']), 'Checking deleteItems.');
	}
}
