<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

/**
 * Tests for the Joomla\Cache\Memcached class.
 *
 * @since  1.0
 */
class MemcachedTest extends CacheTest
{
	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setUp()
	{
		if (!class_exists('Memcached'))
		{
			$this->markTestSkipped(
				'The Memcached class does not exist.'
			);

			return;
		}

		// Parse the DSN details for the test server
		$dsn = defined('JTEST_CACHE_MEMCACHED_DSN') ? JTEST_CACHE_MEMCACHED_DSN : getenv('JTEST_CACHE_MEMCACHED_DSN');

		if ($dsn)
		{
			$options = $this->cacheOptions;

			if (!$options)
			{
				$options = array();
			}

			// First let's trim the memcached: part off the front of the DSN if it exists.
			if (strpos($dsn, 'memcached:') === 0)
			{
				$dsn = substr($dsn, 10);
			}

			if (!is_array($options))
			{
				$options = array($options);
			}

			// Split the DSN into its parts over semicolons.
			$parts = explode(';', $dsn);

			if (!isset($options['memcache.servers']))
			{
				$server = new \stdClass;

				// Parse each part and populate the options array.
				foreach ($parts as $part)
				{
					list ($k, $v) = explode('=', $part, 2);
					switch ($k)
					{
						case 'host':
						case 'port':
							$server->$k = $v;
							break;
					}
				}
				$options['memcache.servers'] = array($server);
			}

			$this->cacheOptions = $options;
		}
		else
		{
			$this->markTestSkipped('No configuration for Memcached given');
		}

		$this->cacheClass = 'Joomla\\Cache\\Memcached';
		parent::setUp();
	}
}
