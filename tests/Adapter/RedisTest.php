<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests\Adapter;

use Joomla\Cache\Adapter\Redis;
use Joomla\Cache\Tests\CacheTestCase;

/**
 * Tests for the Joomla\Cache\Adapter\Redis class.
 */
class RedisTest extends CacheTestCase
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		if (!Redis::isSupported())
		{
			$this->markTestSkipped('Redis Cache Handler is not supported on this system.');
		}

		$driver = new \Redis;

		if (!$driver->connect('127.0.0.1', 6379))
		{
			unset($driver);
			$this->markTestSkipped('Cannot connect to Redis.');
		}

		$this->instance = new Redis($driver, $this->cacheOptions);
	}
}
