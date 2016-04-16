<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Adapter;

use Joomla\Cache\AbstractCacheItemPool;
use Joomla\Cache\Item\HasExpirationDateInterface;
use Joomla\Cache\Item\Item;
use Psr\Cache\CacheItemInterface;

/**
 * Redis cache driver for the Joomla Framework.
 *
 * @since  1.0
 */
class Redis extends AbstractCacheItemPool
{
	/**
	 * The redis driver.
	 *
	 * @var    \Redis
	 * @since  1.0
	 */
	private $driver;

	/**
	 * Constructor.
	 *
	 * @param   \Redis              $redis    The Redis driver being used for this pool
	 * @param   array|\ArrayAccess  $options  An options array, or an object that implements \ArrayAccess
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function __construct(\Redis $redis, $options = [])
	{
		// Parent sets up the caching options and checks their type
		parent::__construct($options);

		$this->driver = $redis;
	}

	/**
	 * This will wipe out the entire cache's keys
	 *
	 * @return  boolean  The result of the clear operation.
	 *
	 * @since   1.0
	 */
	public function clear()
	{
		return $this->driver->flushDB();
	}

	/**
	 * Method to get a storage entry value from a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  CacheItemInterface
	 *
	 * @since   1.0
	 */
	public function getItem($key)
	{
		$value = $this->driver->get($key);
		$item = new Item($key);

		if ($value !== false)
		{
			$item->set($value);
		}

		return $item;
	}

	/**
	 * Method to remove a storage entry for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function deleteItem($key)
	{
		if ($this->hasItem($key))
		{
			return (bool) $this->driver->del($key);
		}

		// If the item doesn't exist, no error
		return true;
	}

	/**
	 * Persists a cache item immediately.
	 *
	 * @param   CacheItemInterface  $item  The cache item to save.
	 *
	 * @return  bool  True if the item was successfully persisted. False if there was an error.
	 */
	public function save(CacheItemInterface $item)
	{
		if ($item instanceof HasExpirationDateInterface)
		{
			$ttl = $this->convertItemExpiryToSeconds($item);

			if ($ttl > 0)
			{
				return $this->driver->setex($item->getKey(), $ttl, $item->get());
			}
		}

		return $this->driver->set($item->getKey(), $item->get());
	}

	/**
	 * Method to determine whether a storage entry has been set for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function hasItem($key)
	{
		return $this->driver->exists($key);
	}

	/**
	 * Test to see if the CacheItemPoolInterface is available
	 *
	 * @return  boolean  True on success, false otherwise
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function isSupported()
	{
		return (extension_loaded('redis') && class_exists('Redis'));
	}
}
