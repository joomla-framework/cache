<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use Psr\Cache\CacheItemInterface;

/**
 * Runtime memory cache driver.
 *
 * @since  1.0
 */
class Runtime extends Cache
{
	/**
	 * The runtime cache storage array.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private static $store = array();

	/**
	 * This will wipe out the entire cache's keys
	 *
	 * @return  boolean  The result of the clear operation.
	 *
	 * @since   1.0
	 */
	public function clear()
	{
		self::$store = array();

		return true;
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

		if (isset(self::$store[$key]))
		{
			$item->set(self::$store[$key]);
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
		unset(self::$store[$key]);

		return true;
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
		self::$store[$item->getKey()] = $item->get();

		return true;
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
		return isset(self::$store[$key]);
	}
}
