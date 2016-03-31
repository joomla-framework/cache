<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use Joomla\Cache\Exception\UnsupportedFormatException;
use Joomla\Cache\Item\HasExpirationDateInterface;
use Psr\Cache\CacheItemInterface;
use Joomla\Cache\Item\Item;

/**
 * WinCache cache driver for the Joomla Framework.
 *
 * @since  1.0
 */
class Wincache extends Cache
{
	/**
	 * Constructor.
	 *
	 * @param   array  $options  Caching options object.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function __construct($options = array())
	{
		if (!extension_loaded('wincache') || !is_callable('wincache_ucache_get'))
		{
			throw new UnsupportedFormatException('WinCache not supported.');
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
		$item = new Item($key);
		$success = true;
		$value = wincache_ucache_get($key, $success);

		if ($success)
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
			return wincache_ucache_delete($key);
		}

		// If the item doesn't exist, no error
		return true;
	}

	/**
	 * Persists a cache item immediately.
	 *
	 * @param   CacheItemInterface  $item  The cache item to save.
	 *
	 * @return  static  The invoked object.
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

		return wincache_ucache_set($item->getKey(), $item->get(), $ttl);
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
		return wincache_ucache_exists($key);
	}
}
