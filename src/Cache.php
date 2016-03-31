<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use Joomla\Cache\Exception\InvalidArgumentException;
use Joomla\Cache\Item\HasExpirationDateInterface;
use Psr\Cache\CacheItemInterface;

/**
 * Joomla! Caching Class
 *
 * @since  1.0
 */
abstract class Cache implements CacheItemPoolInterface
{
	/**
	 * The options for the cache object.
	 *
	 * @var    array|\ArrayAccess
	 * @since  1.0
	 */
	protected $options;

	/**
	 * The deferred items to store
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $deferred = [];

	/**
	 * Constructor.
	 *
	 * @param   array|\ArrayAccess  $options  An options array, or an object that implements \ArrayAccess
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function __construct($options = [])
	{
		if (!($options instanceof \ArrayAccess || is_array($options)))
		{
			throw new InvalidArgumentException(sprintf('%s requires an options array or an object that implements \\ArrayAccess', __CLASS__));
		}

		$this->options = $options;
	}

	/**
	 * Returns a traversable set of cache items.
	 *
	 * @param   array  $keys  A list of keys that can obtained in a single operation.
	 *
	 * @return  CacheItemInterface[]  An associative array of CacheItemInterface objects keyed on the cache key.
	 *
	 * @since   1.0
	 */
	public function getItems(array $keys = [])
	{
		$result = [];

		foreach ($keys as $key)
		{
			$result[$key] = $this->getItem($key);
		}

		return $result;
	}

	/**
	 * Get an option from the Cache instance.
	 *
	 * @param   string  $key  The name of the option to get.
	 *
	 * @return  mixed  The option value.
	 *
	 * @since   1.0
	 */
	public function getOption($key)
	{
		return isset($this->options[$key]) ? $this->options[$key] : null;
	}

	/**
	 * Removes the item from the pool.
	 *
	 * @param   string  $key  The key for which to delete
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	abstract public function deleteItem($key);

	/**
	 * Removes multiple items from the pool.
	 *
	 * @param   array  $keys  An array of keys that should be removed from the pool.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function deleteItems(array $keys)
	{
		$result = true;

		foreach ($keys as $key)
		{
			if (!$this->deleteItem($key))
			{
				$result = false;
			}
		}

		return $result;
	}

	/**
	 * Set an option for the Cache instance.
	 *
	 * @param   string  $key    The name of the option to set.
	 * @param   mixed   $value  The option value to set.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;

		return $this;
	}

	/**
	 * Sets a cache item to be persisted later.
	 *
	 * @param   CacheItemInterface  $item  The cache item to save.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function saveDeferred(CacheItemInterface $item)
	{
		$this->deferred[$item->getKey()] = $item;

		return $this;
	}

	/**
	 * Persists any deferred cache items.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function commit()
	{
		$result = true;

		foreach ($this->deferred as $key => $deferred)
		{
			$saveResult = $this->save($deferred);

			if (true === $saveResult)
			{
				unset($this->deferred[$key]);
			}

			$result = $result && $saveResult;
		}

		return $result;
	}

	/**
	 * Converts a DateTime object from the cache item to the expiry time in seconds from the present
	 *
	 * @param   HasExpirationDateInterface  $item  The cache item
	 *
	 * @return  integer  The time in seconds until expiry
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function convertItemExpiryToSeconds(HasExpirationDateInterface $item)
	{
		$itemExpiry   = $item->getExpiration();
		$itemTimezone = $itemExpiry->getTimezone();
		$now          = new \DateTime('now', $itemTimezone);
		$interval     = $now->diff($itemExpiry);

		return (int) $interval->format('%s');
	}
}
