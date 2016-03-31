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
use Joomla\Cache\Item\Item;
use Psr\Cache\CacheItemInterface;
use Redis as RedisDriver;

/**
 * Redis cache driver for the Joomla Framework.
 *
 * @since  1.0
 */
class Redis extends Cache
{
	/**
	 * Default hostname of redis server
	 */
	const REDIS_HOST = '127.0.0.1';

	/**
	 * Default port of redis server
	 */
	const REDIS_PORT = 6379;

	/**
	 * @var    \Redis  The redis driver.
	 * @since  1.0
	 */
	private $driver;

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
		if (!extension_loaded('redis') || !class_exists('\Redis'))
		{
			throw new UnsupportedFormatException('Redis not supported.');
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
		$this->connect();

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
		$this->connect();

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
		$this->connect();

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
		$this->connect();

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
		$this->connect();

		return $this->driver->exists($key);
	}

	/**
	 * Connect to the Redis servers if the connection does not already exist.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	private function connect()
	{
		// We want to only create the driver once.
		if (isset($this->driver))
		{
			return;
		}

		$host = isset($this->options['redis.host'])? $this->options['redis.host'] : self::REDIS_HOST;
		$port = isset($this->options['redis.port'])? $this->options['redis.port'] : self::REDIS_PORT;

		$this->driver = new RedisDriver;

		if (($host == 'localhost' || filter_var($host, FILTER_VALIDATE_IP)))
		{
			$this->driver->connect('tcp://' . $host . ':' . $port, $port);
		}
		else
		{
			$this->driver->connect($host, null);
		}
	}
}
