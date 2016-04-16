<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests\Adapter;

use Joomla\Cache\Adapter\File;
use Joomla\Cache\Tests\CacheTest;

/**
 * Tests for the Joomla\Cache\Adapter\File class.
 */
class FileTest extends CacheTest
{
	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		$options = array_merge(
			$this->cacheOptions, ['file.path' => dirname(__DIR__) . '/tmp']
		);

		$this->instance = new File($options);
	}
}
