<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use Joomla\Cache\Exception\UnsupportedFormatException;
use Psr\Cache\CacheItemInterface;

/**
 * APC cache driver for the Joomla Framework.
 *
 * @since  1.0
 */
class Apc extends Cache
{
	/**
	 * Constructor.
	 *
	 * @param   mixed  $options  An options array, or an object that implements \ArrayAccess
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function __construct($options = array())
	{
		if (!extension_loaded('apc') || !is_callable('apc_fetch'))
		{
			throw new UnsupportedFormatException('APC not supported.');
		}

		parent::__construct($options);
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
		return apc_clear_cache('user');
	}

	/**
	 * Method to get a storage entry value from a key.
	 *
	 * @param   string  $key  The storage entry identifier.
	 *
	 * @return  CacheItemInterface
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function getItem($key)
	{
		$success = false;
		$value = apc_fetch($key, $success);
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
	 * @since   1.0
	 */
	public function getItems(array $keys = array())
	{
		$items = array();
		$success = false;
		$values = apc_fetch($keys, $success);

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
	 * @since   1.0
	 */
	public function deleteItem($key)
	{
		return apc_delete($key);
	}

	/**
	 * Persists a cache item immediately.
	 *
	 * @param CacheItemInterface $item
	 *   The cache item to save.
	 *
	 * @return static
	 *   The invoked object.
	 */
	public function save(CacheItemInterface $item)
	{
		return apc_store($item->getKey(), $item->get(), $this->convertItemExpiryToSeconds($item));
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
		return apc_exists($key);
	}
}
