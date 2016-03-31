<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use Psr\Cache\CacheItemInterface;
use Joomla\Cache\Item\Item;

/**
 * Runtime memory cache driver.
 *
 * @since  1.0
 */
class Runtime extends Cache
{
	/**
	 * @var    \ArrayObject  Database of cached items, we use ArrayObject so it can be easily
	 *                       passed by reference
	 *
	 * @since  2.0
	 */
	private $db;

	/**
	 * Constructor.
	 *
	 * @param   mixed  $options  An options array, or an object that implements \ArrayAccess
	 *
	 * @since   2.0
	 * @throws  \RuntimeException
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);
		$this->db = new \ArrayObject;
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
		// Replace the db with a new blank array
		$clearData = $this->db->exchangeArray(array());
		unset($clearData);

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

		if ($this->hasItem($key))
		{
			$item->set($this->db[$key]);
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
			$newCache = array_diff_key($this->db->getArrayCopy(), array($key => $key));
			$this->db->exchangeArray($newCache);
		}

		return true;
	}

	/**
	 * Method to set a value for a storage entry.
	 *
	 * @param   CacheItemInterface  $item  The cache item to save.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function save(CacheItemInterface $item)
	{
		$this->db[$item->getKey()] = $item->get();

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
		return array_key_exists($key, $this->db);
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
		return true;
	}
}
