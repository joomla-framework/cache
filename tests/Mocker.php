<?php
/**
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class to mock Joomla\Mocker\Cache.
 *
 * @since  1.0
 */
class Mocker
{
	/**
	 * @var    TestCase
	 * @since  1.0
	 */
	private $test;

	/**
	 * Class contructor.
	 *
	 * @param   TestCase  $test  A test class.
	 *
	 * @since   1.0
	 */
	public function __construct(TestCase $test)
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
<<<<<<< HEAD:tests/Mocker.php
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
=======
		$mockObject = $this->test->getMockBuilder('Joomla\Cache\Cache')
			->setMethods($methods)
			->setConstructorArgs(array())
			->disableOriginalConstructor()
			->getMock();
>>>>>>> 96d481ac7404755d03b5b4926addd8925e142155:Tests/Mocker.php

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
<<<<<<< HEAD:tests/Mocker.php
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
=======
		$mockObject = $this->test->getMockBuilder('Joomla\Cache\Item')
			->setMethods($methods)
			->setConstructorArgs(array())
			->disableOriginalConstructor()
			->getMock();
>>>>>>> 96d481ac7404755d03b5b4926addd8925e142155:Tests/Mocker.php

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
