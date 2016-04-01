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
class RedisTest extends CacheTest
{
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
		if (!Cache\Redis::isSupported())
		{
			$this->markTestSkipped('Redis Cache Handler is not supported on this system.');
		}

		$this->cacheClass = 'Joomla\\Cache\\Redis';
		parent::setUp();

		$this->instance->clear();
	}
}
