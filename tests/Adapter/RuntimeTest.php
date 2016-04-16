<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests\Adapter;

use Joomla\Cache\Adapter\Runtime;
use Joomla\Cache\Tests\CacheTest;

/**
 * Tests for the Joomla\Cache\Adapter\Runtime class.
 *
 * @since  1.0
 */
class RuntimeTest extends CacheTest
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->instance = new Runtime($this->cacheOptions);
	}

	/**
	 * Tests the Joomla\Cache\Cache::getItem and Joomla\Cache\Cache::save methods with timeout
	 */
	public function testGetAndSaveWithTimeout()
	{
		$this->markTestSkipped('Runtime cache currently does not support timeouts');
	}

}
