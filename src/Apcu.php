<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use Joomla\Cache\Item\HasExpirationDateInterface;
use Psr\Cache\CacheItemInterface;
use Joomla\Cache\Item\Item;

/**
 * APCu cache driver for the Joomla Framework.
 *
 * @since  __DEPLOY_VERSION__
 */
class Apcu extends Cache
{
	/**
	 * This will wipe out the entire cache's keys
	 *
	 * @return  boolean  The result of the clear operation.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function clear()
	{
		return apcu_clear_cache();
	}

	/**
	 * Method to get a storage entry value from a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  CacheItemInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \RuntimeException
	 */
	public function getItem($key)
	{
		$success = false;
		$value = apcu_fetch($key, $success);
		$item = new Item($key);

		if ($success)
		{
			$item->set($value);
		}

		return $item;
	}

	/**
	 * Obtain multiple CacheItems by their unique keys.
	 *
	 * @param   array  $keys  A list of keys that can obtained in a single operation.
	 *
	 * @return  array  An associative array of CacheItem objects keyed on the cache key.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getItems(array $keys = array())
	{
		$items = array();
		$success = false;
		$values = apcu_fetch($keys, $success);

		if ($success && is_array($values))
		{
			foreach ($keys as $key)
			{
				$items[$key] = new Item($key);

				if (isset($values[$key]))
				{
					$items[$key]->set($values[$key]);
				}
			}
		}

		return $items;
	}

	/**
	 * Method to remove a storage entry for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function deleteItem($key)
	{
		if ($this->hasItem($key))
		{
			return apcu_delete($key);
		}

		// If the item doesn't exist, no error
		return true;
	}

	/**
	 * Persists a cache item immediately.
	 *
	 * @param   CacheItemInterface  $item  The cache item to save.
	 *
	 * @return static
	 *   The invoked object.
	 */
	public function save(CacheItemInterface $item)
	{
		// If we are able to find out when the item expires - find out. Else bail.
		if ($item instanceof HasExpirationDateInterface)
		{
			$ttl = $this->convertItemExpiryToSeconds($item);
		}
		else
		{
			$ttl = 0;
		}

		return apcu_store($item->getKey(), $item->get(), $ttl);
	}

	/**
	 * Method to determine whether a storage entry has been set for a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function hasItem($key)
	{
		return apcu_exists($key);
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
		$supported = extension_loaded('apcu') && ini_get('apc.enabled');

		// If on the CLI interface, the `apc.enable_cli` option must also be enabled
		if ($supported && php_sapi_name() === 'cli')
		{
			$supported = ini_get('apc.enable_cli');
		}

		return (bool) $supported;
	}
}
