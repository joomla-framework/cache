<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use Joomla\Cache\Exception\InvalidArgumentException;
use DateTime;

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
	 * @var    \ArrayAccess
	 * @since  1.0
	 */
	protected $options;

	/**
	 * The deferred items to store
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $deferred = array();

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
		if (! ($options instanceof \ArrayAccess || is_array($options)))
		{
			throw new InvalidArgumentException(sprintf('%s requires an options array or an object that implements \\ArrayAccess', __CLASS__));
		}

		$this->options = $options;
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
		$result = array();

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
	 * Delete a cached data entry by id.
	 *
	 * @param   string  $key  The cache data id.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	abstract public function deleteItem($key);

	/**
	 * Remove multiple cache items in a single operation.
	 *
	 * @param   array  $keys  The array of keys to be removed.
	 *
	 * @return  array  An associative array of 'key' => result, elements. Each array row has the key being deleted
	 *                 and the result of that operation. The result will be a boolean of true or false
	 *                 representing if the cache item was removed or not
	 *
	 * @since   1.0
	 */
	public function deleteItems(array $keys)
	{
		$result = array();

		foreach ($keys as $key)
		{
			$result[$key] = $this->deleteItem($key);
		}

		return $result;
	}

	/**
	 * Set an option for the Cache instance.
	 *
	 * @param   string  $key    The name of the option to set.
	 * @param   mixed   $value  The option value to set.
	 *
	 * @return  Cache  This object for method chaining.
	 *
	 * @since   1.0
	 */
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;

		return $this;
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
	abstract protected function exists($key);

	/**
	 * Sets a cache item to be persisted later.
	 *
	 * @param CacheItemInterface $item
	 *   The cache item to save.
	 * @return static
	 *   The invoked object.
	 */
	public function saveDeferred(CacheItemInterface $item)
	{
		$this->deferred[$item->getKey()] = $item;

		return $this;
	}

	/**
	 * Persists any deferred cache items.
	 *
	 * @return bool
	 *   TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
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
	 * Converts the DateTime object from the cache item to the expiry time in seconds from the present
	 *
	 * @param  CacheItemInterface $item  The cache item
	 *
	 * @return int  The time in seconds until expiry
	 */
	protected function convertItemExpiryToSeconds(CacheItemInterface $item)
	{
		$itemExpiry = $item->getExpiration();
		$itemTimezone = $itemExpiry->getTimezone();
		$now = new DateTime('now', $itemTimezone);
		$interval = $now->diff($itemExpiry);

		return (int) $interval->format('%s');
	}
}
