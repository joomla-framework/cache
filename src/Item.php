<?php
/**
 * Part of the Joomla Framework Cache Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache;

use DateTime;
use Psr\Cache\CacheItemInterface;

/**
 * Cache item instance for the Joomla Framework.
 *
 * @since  1.0
 */
class Item implements CacheItemInterface
{
	/**
	 * The time the object expires at
	 *
	 * @var    DateTime
	 * @since  2.0
	 */
	private $expiration;

	/**
	 * The key for the cache item.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $key;

	/**
	 * The value of the cache item.
	 *
	 * @var    mixed
	 * @since  1.0
	 */
	private $value = null;

	/**
	 * Whether the cache item is value or not.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	private $hit = false;

	/**
	 * Class constructor.
	 *
	 * @param   string                $key  The key for the cache item.
	 * @param   DateTime|integer|null $ttl  The expiry time for the cache item in seconds or as a datetime object
	 *
	 * @since   1.0
	 */
	public function __construct($key, $ttl = null)
	{
		$this->key = $key;

		if (is_int($ttl))
		{
			$this->expiresAfter($ttl);
		}
		elseif ($ttl instanceof DateTime)
		{
			$this->expiresAt($ttl);
		}
		else
		{
			$this->expiresAfter(900);
		}
	}


	/**
	 * Confirms if the cache item exists in the cache.
	 *
	 * Note: This method MAY avoid retrieving the cached value for performance
	 * reasons, which could result in a race condition between exists() and get().
	 * To avoid that potential race condition use isHit() instead.
	 *
	 * @return boolean
	 *  True if item exists in the cache, false otherwise.
	 */
	public function exists()
	{
		return $this->isHit();
	}

	/**
	 * Get the key associated with this CacheItem.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Obtain the value of this cache item.
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function get()
	{
		return $this->value;
	}

	/**
	 * Set the value of the item.
	 *
	 * If the value is set, we are assuming that there was a valid hit on the cache for the given key.
	 *
	 * @param   mixed  $value  The value for the cache item.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function set($value)
	{
		$this->value = $value;
		$this->hit = true;
	}

	/**
	 * This boolean value tells us if our cache item is currently in the cache or not.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function isHit()
	{
		return $this->hit;
	}

	/**
	 * Sets the expiration time for this cache item.
	 *
	 * @param \DateTimeInterface $expiration
	 *   The point in time after which the item MUST be considered expired.
	 *   If null is passed explicitly, a default value MAY be used. If none is set,
	 *   the value should be stored permanently or for as long as the
	 *   implementation allows.
	 *
	 * @return static
	 *   The called object.
	 */
	public function expiresAt($expiration)
	{
		$this->expiration = $expiration;
	}

	/**
	 * Sets the expiration time for this cache item.
	 *
	 * @param int|\DateInterval $time
	 *   The period of time from the present after which the item MUST be considered
	 *   expired. An integer parameter is understood to be the time in seconds until
	 *   expiration.
	 *
	 * @return static
	 *   The called object.
	 */
	public function expiresAfter($time)
	{
		if (is_integer($time))
		{
			$this->expiration = new DateTime('now +' . $time . ' seconds');
		}
		elseif($time instanceof \DateInterval)
		{
			$this->expiration = new DateTime('now');
			$this->expiration->add($time);
		}
		else
		{
			$this->expiration = new DateTime('now + 900 seconds');
		}
	}

	/**
	 * Returns the expiration time of a not-yet-expired cache item.
	 *
	 * If this cache item is a Cache Miss, this method MAY return the time at
	 * which the item expired or the current time if that is not available.
	 *
	 * @return \DateTime
	 *   The timestamp at which this cache item will expire.
	 */
	public function getExpiration()
	{
		return $this->expiration;
	}
}
