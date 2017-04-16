<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Cache\Wincache class.
 *
 * @since  1.0
 */
class WincacheTest extends TestCase
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
		$this->assertInstanceOf('\Psr\Cache\CacheItemInterface', $this->instance->get('foo'), 'Checking get.');
		$this->assertInternalType('array', $this->instance->getMultiple(array('foo')), 'Checking getMultiple.');
		$this->assertInternalType('boolean', $this->instance->remove('foo'), 'Checking remove.');
		$this->assertInternalType('array', $this->instance->removeMultiple(array('foo')), 'Checking removeMultiple.');
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
		$this->assertTrue($this->instance->clear());
	}

	/**
	 * Tests the Joomla\Cache\Wincache::exists method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::exists
	 * @since   1.0
	 */
	public function testExists()
	{
		$this->assertTrue($this->instance->exists('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Wincache::get method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::get
	 * @since   1.0
	 */
	public function testGet()
	{
		$this->assertInstanceOf('\Psr\Cache\CacheItemInterface', $this->instance->get('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Wincache::remove method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Wincache::remove
	 * @since   1.0
	 */
	public function testRemove()
	{
		$this->assertTrue($this->instance->remove('foo'));
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
		$this->assertTrue($this->instance->set('foo', 'bar'));
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
			$this->instance->set('foo', 'bar');
		}
		catch (\Exception $e)
		{
			$this->markTestSkipped();
		}
	}
}
