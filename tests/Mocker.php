<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Test\TestHelper;

/**
 * Class to mock Joomla\Mocker\Cache.
 *
 * @since  1.0
 */
class Mocker
{
	/**
	 * @var    \PHPUnit_Framework_TestCase
	 * @since  1.0
	 */
	private $test;

	/**
	 * Class contructor.
	 *
	 * @param   \PHPUnit_Framework_TestCase  $test  A test class.
	 *
	 * @since   1.0
	 */
	public function __construct(\PHPUnit_Framework_TestCase $test)
	{
		$this->test = $test;
	}

	/**
	 * Creates and instance of a mock Joomla\Mocker\Cache object.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function createMockCache()
	{
		// Collect all the relevant methods in JDatabase.
		$methods = array(
			'clear',
			'hasItem',
			'getItem',
			'getItems',
			'deleteItem',
			'deleteItems',
			'save'
		);

		// Create the mock.
		$mockObject = $this->test->getMock(
			'Joomla\Cache\AbstractCacheItemPool',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		TestHelper::assignMockCallbacks(
			$mockObject,
			$this->test,
			array(
				'getItem' => array((is_callable(array($this->test, 'mockCacheGetItem')) ? $this->test : $this), 'mockCacheGetItem'),
			)
		);

		return $mockObject;
	}

	/**
	 * Creates and instance of a mock Joomla\Cache\Item object.
	 *
	 * @return  object
	 *
	 * @since   1.0
	 */
	public function createMockItem()
	{
		// Collect all the relevant methods in JDatabase.
		$methods = array(
			'getKey',
			'get',
			'isHit',
			'set',
		);

		// Create the mock.
		$mockObject = $this->test->getMock(
			'Joomla\Cache\Item\Item',
			$methods,
			// Constructor arguments.
			array(),
			// Mock class name.
			'',
			// Call original constructor.
			false
		);

		TestHelper::assignMockCallbacks(
			$mockObject,
			$this->test,
			array(
				'get' => array((is_callable(array($this->test, 'mockCacheItemGet')) ? $this->test : $this), 'mockCacheItemGet'),
				'isHit' => array((is_callable(array($this->test, 'mockCacheItemIsHit')) ? $this->test : $this), 'mockCacheItemIsHit'),
			)
		);

		return $mockObject;
	}

	/**
	 * Callback to mock the Joomla\Cache\Cache::get method.
	 *
	 * @param   string  $key  The input text.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function mockCacheGetItem($key)
	{
		return $this->createMockItem();
	}

	/**
	 * Callback to mock the Joomla\Cache\Item::getValue method.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function mockCacheItemGet()
	{
		return 'value';
	}

	/**
	 * Callback to mock the Joomla\Cache\Item::isHit method.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function mockCacheItemIsHit()
	{
		return false;
	}
}
