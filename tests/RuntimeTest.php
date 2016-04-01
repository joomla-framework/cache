<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;

/**
 * Tests for the Joomla\Cache\Runtime class.
 *
 * @since  1.0
 */
class RuntimeTest extends CacheTest
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
		if (!Cache\Runtime::isSupported())
		{
			$this->markTestSkipped('Runtime Cache Handler is not supported on this system.');
		}

		$this->cacheClass = 'Joomla\\Cache\\Runtime';
		parent::setUp();
	}
}
