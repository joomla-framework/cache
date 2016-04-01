<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Redis class.
 */
class RedisTest extends CacheTest
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		if (!Cache\Redis::isSupported())
		{
			$this->markTestSkipped('Redis Cache Handler is not supported on this system.');
		}

		$driver = new \Redis;

		if (!$driver->connect('127.0.0.1', 6379))
		{
			unset($driver);
			$this->markTestSkipped('Cannot connect to Redis.');
		}

		$this->instance = new Cache\Redis($driver, $this->cacheOptions);
	}
}
