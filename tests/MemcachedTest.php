<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Memcached class.
 */
class MemcachedTest extends CacheTest
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		if (!Cache\Memcached::isSupported())
		{
			$this->markTestSkipped('Memcached Cache Handler is not supported on this system.');
		}

		$options = array_merge(
			$this->cacheOptions, ['memcache.servers' => [(object) ['host' => 'localhost', 'port' => 11211]]]
		);

		$this->instance = new Cache\Memcached($options);
	}
}
