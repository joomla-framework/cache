<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache\Item\Item;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Cache\Item class.
 *
 * @since  1.0
 */
class ItemTest extends TestCase
{
	/**
	 * @var    Item
	 * @since  1.0
	 */
	private $instance;

	/**
	 * Tests the Joomla\Cache\Item class.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testItem()
	{
		$this->assertEquals('foo', $this->instance->getKey());
		$this->assertNull($this->instance->get());
		$this->assertFalse($this->instance->isHit());

		$this->instance->set('bar');
		$this->assertEquals('bar', $this->instance->get());
		$this->assertTrue($this->instance->isHit());
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

		$this->instance = new Item('foo');
	}
}
