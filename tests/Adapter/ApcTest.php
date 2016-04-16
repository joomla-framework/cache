<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests\Adapter;

use Joomla\Cache\Adapter\Apc;
use Joomla\Cache\Tests\CacheTestCase;

/**
 * Tests for the Joomla\Cache\Adapter\Apc class.
 */
class ApcTest extends CacheTestCase
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		if (!Apc::isSupported())
		{
			$this->markTestSkipped('APC Cache Handler is not supported on this system.');
		}

		$this->instance = new Apc($this->cacheOptions);
	}

	/**
	 * Tests the Joomla\Cache\Cache::getItem and Joomla\Cache\Cache::save methods with timeout
	 */
	public function testGetAndSaveWithTimeout()
	{
		$this->markTestSkipped('The APC cache TTL is not working in a single process/request. See https://bugs.php.net/bug.php?id=58084');
	}
}
