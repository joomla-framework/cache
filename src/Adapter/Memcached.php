<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Adapter;

use Joomla\Cache\AbstractCacheItemPool;
use Joomla\Cache\Exception\RuntimeException;
use Joomla\Cache\Item\HasExpirationDateInterface;
use Joomla\Cache\Item\Item;
use Psr\Cache\CacheItemInterface;

/**
 * Memcached cache driver for the Joomla Framework.
 *
 * @since  1.0
 */
class Memcached extends AbstractCacheItemPool
{
	/**
	 * The Memcached driver
	 *
	 * @var    \Memcached
	 * @since  1.0
	 */
	protected $driver;

	/**
	 * Constructor.
	 *
	 * @param   \Memcached          $memcached  The Memcached driver being used for this pool
	 * @param   array|\ArrayAccess  $options    An options array, or an object that implements \ArrayAccess
	 *
	 * @since   1.0
	 */
	public function __construct(\Memcached $memcached, $options = [])
	{
		// Parent sets up the caching options and checks their type
		parent::__construct($options);

		$this->driver = $memcached;
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
		return $this->driver->flush();
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
		$code = $this->driver->getResultCode();
		$item = new Item($key);

		if ($code === \Memcached::RES_SUCCESS)
		{
			$item->set($value);
		}

		return $item;
	}

	/**
	 * Returns a traversable set of cache items.
	 *
	 * @param   array  $keys  A list of keys that can obtained in a single operation.
	 *
	 * @return  CacheItemInterface[]  An associative array of CacheItemInterface objects keyed on the cache key.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getItems(array $keys = [])
	{
		$data = $this->driver->getMulti($keys);

		$result = [];

		foreach ($keys as $key)
		{
			$item = new Item($key);

			// On some platforms $data may be a boolean false
			if (is_array($data) && array_key_exists($key, $data))
			{
				$item->set($data[$key]);
			}

			$result[$key] = $item;
		}

		return $result;
	}

	/**
	 * Method to remove a storage entry for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 */
	public function deleteItem($key)
	{
		if ($this->hasItem($key))
		{
			$this->driver->delete($key);

			$rc = $this->driver->getResultCode();

			// If the item was not successfully removed nor did not exist then raise an error
			if (($rc !== \Memcached::RES_SUCCESS))
			{
				throw new RuntimeException(sprintf('Unable to remove cache entry for %s. Error message `%s`.', $key, $this->driver->getResultMessage()));
			}
		}

		return true;
	}

	/**
	 * Removes multiple items from the pool.
	 *
	 * @param   array  $keys  An array of keys that should be removed from the pool.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function deleteItems(array $keys)
	{
		// HHVM doesn't support deleteMulti
		if (!method_exists($this->driver, 'deleteMulti'))
		{
			return parent::deleteItems($keys);
		}

		$deleted = $this->driver->deleteMulti($keys);

		foreach ($deleted as $key => $value)
		{
			/*
			 * The return of deleteMulti is not consistent with the documentation for error cases,
			 * so check for an explicit boolean true for successful deletion
			 */
			if ($value !== true && $value !== \Memcached::RES_NOTFOUND)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Persists a cache item immediately.
	 *
	 * @param   CacheItemInterface  $item  The cache item to save.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function save(CacheItemInterface $item)
	{
		if ($item instanceof HasExpirationDateInterface)
		{
			$ttl = $this->convertItemExpiryToSeconds($item);
		}
		else
		{
			$ttl = 0;
		}

		$this->driver->set($item->getKey(), $item->get(), $ttl);

		return (bool) ($this->driver->getResultCode() == \Memcached::RES_SUCCESS);
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
		$this->driver->get($key);

		return ($this->driver->getResultCode() !== \Memcached::RES_NOTFOUND);
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
		/*
		 * GAE and HHVM have both had instances where Memcached the class was defined but no extension was loaded.
		 * If the class is there, we can assume it works.
		 */
		return (class_exists('Memcached'));
	}
}
