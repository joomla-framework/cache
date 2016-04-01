<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\None class.
 *
 * @since  1.0
 */
class NoneTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Cache\None
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
	 * Tests the Joomla\Cache\None::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\None::clear
	 * @since   1.0
	 */
	public function testClear()
	{
		$this->instance->clear();
	}

	/**
	 * Tests the Joomla\Cache\None::getItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\None::getItem
	 * @since   1.0
	 */
	public function testGetItem()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$item = $this->instance->getItem('foo');
		$this->assertNull($item->get());
		$this->assertFalse($item->isHit());
	}

	/**
	 * Tests the Joomla\Cache\None::deleteItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\None::deleteItem
	 * @since   1.0
	 */
	public function testDeleteItem()
	{
		$this->instance->deleteItem('foo');
	}

	/**
	 * Tests the Joomla\Cache\None::save method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\None::save
	 * @since   1.0
	 */
	public function testSet()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$item = $this->instance->getItem('foo');
		$this->assertNull($item->get());
		$this->assertFalse($item->isHit());
	}

	/**
	 * Tests the Joomla\Cache\None::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\None::hasItem
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
		$this->assertFalse($this->instance->hasItem('foo'));
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

		$this->instance = new Cache\None;
	}
}
