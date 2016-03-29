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
		$this->assertInternalType('array', $this->instance->deleteItems(array('foo')), 'Checking removeMultiple.');
		$this->assertInternalType('boolean', $this->instance->set('for', 'bar'), 'Checking set.');
		$this->assertInternalType('boolean', $this->instance->setMultiple(array('foo' => 'bar')), 'Checking setMultiple.');
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
		$this->instance->setMultiple(
			array(
				'foo' => 'bar',
				'goo' => 'car',
			)
		);

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
		$this->instance->set('foo', 'bar');
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

		$this->instance->set('foo', 'bar');
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
		$this->instance->set('foo', 'bar');
		$this->assertEquals('bar', $this->instance->getItem('foo')->get());

		$this->instance->deleteItem('foo');
		$this->assertNull($this->instance->getItem('foo')->get());
	}

	/**
	 * Tests the Joomla\Cache\Runtime::set method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Runtime::set
	 * @since   1.0
	 */
	public function testSet()
	{
		$this->instance->set('foo', 'bar');
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
