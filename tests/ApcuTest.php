<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Apcu class.
 *
 * @since  1.0
 */
class ApcuTest extends CacheTest
{
	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$this->cacheClass = 'Joomla\\Cache\\Apcu';

		parent::setUp();
	}

	/**
	 * Tests the Joomla\Cache\Cache::getItem and Joomla\Cache\Cache::save methods with timeout
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetAndSaveWithTimeout()
	{
		$this->markTestSkipped('The APC cache TTL is not working in a single process/request. See https://bugs.php.net/bug.php?id=58084');
	}
}
