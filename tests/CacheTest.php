<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Psr\Cache\CacheItemInterface;

/**
 * Tests for the Joomla\Cache\Cache class.
 *
 * @since  1.0
 */
abstract class CacheTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    \Joomla\Cache\Cache
	 * @since  1.0
	 */
	public $instance;

	/**
	 * @var    string  Cache Classname to test
	 * @since  1.0
	 */
	public $cacheClass = '';

	/**
	 * @var    array
	 * @since  1.0
	 */
	public $cacheOptions = array('foo' => 900);

	/**
	 * Tests the registry options is correctly initialised.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::__construct
	 * @covers  Joomla\Cache\Apc::__construct
	 * @covers  Joomla\Cache\Memcached::__construct
	 *
	 * @since   1.0
	 */
	public function test__construct()
	{
		$this->assertEquals('900', $this->instance->getOption('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Cache::__construct method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::__construct
	 * @covers  Joomla\Cache\Apc::__construct
	 * @covers  Joomla\Cache\Memcached::__construct
	 * @expectedException  \Joomla\Cache\Exception\InvalidArgumentException
	 * @since   1.0
	 */
	public function test__constructWithInvalidParams()
	{
		// Throws exception, options is null
		$className = $this->cacheClass;
		new $className(null);
	}

	/**
	 * Tests the Joomla\Cache\Cache::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::clear
	 * @covers  Joomla\Cache\Memcached::clear
	 * @since   1.1.3
	 */
	public function testClear()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$this->assertFalse(
			$cacheInstance->hasItem('foobar'),
			__LINE__
		);

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		// Configure the stub.
		$stub->method('get')
			->willReturn('barfoo');

		// Configure the stub.
		$stub->method('getKey')
			->willReturn('foobar');

		// Create a stub for the CacheItemInterface class.
		$stub2 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub2->method('get')
			->willReturn('car');

		$stub2->method('getKey')
			->willReturn('boo');

		$this->assertTrue(
			$cacheInstance->save($stub),
			'Checks first item was saved.'
		);

		$this->assertTrue(
			$cacheInstance->save($stub2),
			'Checks second item was saved.'
		);

		$this->assertTrue(
			$cacheInstance->hasItem('foobar'),
			'Checks first item was saved into adapter.'
		);

		$this->assertTrue(
			$cacheInstance->clear(),
			'Checks clear returns true'
		);

		$this->assertFalse(
			$cacheInstance->hasItem('foobar'),
			'Checks first item was cleared.'
		);

		$this->assertFalse(
			$cacheInstance->hasItem('boo'),
			'Checks second item was cleared.'
		);
	}

	/**
	 * Tests the the Joomla\Cache\Cache::getItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Memcached::getItem
	 * @covers  Joomla\Cache\Memcached::connect
	 * @since   1.0
	 */
	public function testGetItem()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$cacheInstance->save($stub);
		$this->hitKey('foo', 'bar');
		$this->missKey('foobar', 'foobar');
	}

	/**
	 * Checks to ensure a that $key is not set at all in the Cache
	 *
	 * @param   string  $key    Key of cache item to check
	 * @param   string  $value  Value cache item should be
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	protected function missKey($key = '', $value = '')
	{
		$cacheInstance = $this->instance;
		$cacheItem = $cacheInstance->getItem($key);
		$cacheValue = $cacheItem->get();
		$cacheKey = $cacheItem->getKey();
		$cacheHit = $cacheItem->isHit();
		$this->assertThat($cacheKey, $this->equalTo($key), __LINE__);
		$this->assertNull($cacheValue,  __LINE__);
		$this->assertFalse($cacheHit, __LINE__);
	}

	/**
	 * Checks to ensure a that $key is set to $value in the Cache
	 *
	 * @param   string  $key    Key of cache item to check
	 * @param   string  $value  Value cache item should be
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	protected function hitKey($key = '', $value = '')
	{
		$cacheInstance = $this->instance;
		$cacheItem = $cacheInstance->getItem($key);
		$cacheKey = $cacheItem->getKey();
		$cacheValue = $cacheItem->get();
		$cacheHit = $cacheItem->isHit();
		$this->assertThat($cacheKey, $this->equalTo($key), __LINE__);
		$this->assertThat($cacheValue, $this->equalTo($value), __LINE__);
		$this->assertTrue($cacheHit, __LINE__);
	}

	/**
	 * Tests the Joomla\Cache\Cache::save method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::save
	 * @covers  Joomla\Cache\Memcached::save
	 * @covers  Joomla\Cache\Memcached::connect
	 * @since   1.0
	 */
	public function testSave()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('barSet');

		$stub->method('getKey')
			->willReturn('fooSet');

		$this->assertTrue(
			$cacheInstance->save($stub),
			'Save should return true for a valid item'
		);

		$fooValue = $cacheInstance->getItem('fooSet')->get();
		$this->assertThat($fooValue, $this->equalTo('barSet'), __LINE__);
	}

	/**
	 * Tests the Joomla\Cache\Cache::getItems method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::getItems
	 * @covers  Joomla\Cache\Apc::getItems
	 * @since   1.0
	 */
	public function testGetItems()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('foo');

		$stub->method('getKey')
			->willReturn('foo');

		// Create a stub for the CacheItemInterface class.
		$stub2 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub2->method('get')
			->willReturn('bar');

		$stub2->method('getKey')
			->willReturn('bar');

		// Create a stub for the CacheItemInterface class.
		$stub3 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub3->method('get')
			->willReturn('world');

		$stub3->method('getKey')
			->willReturn('hello');

		$samples = array($stub, $stub2, $stub3);
		$expectedSamples = array('foo' => 'foo', 'bar' => 'bar', 'hello' => 'world');
		$moreSamples = $samples;

		// Create a stub for the CacheItemInterface class.
		$stub4 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub4->method('get')
			->willReturn('bar');

		$stub4->method('getKey')
			->willReturn('next');

		$moreSamples[] = $stub4;
		$lessSamples = $samples;
		$badSampleKeys = array('foobar', 'barfoo', 'helloworld');

		// Pop an item from the array
		array_pop($lessSamples);

		$keys = array('foo', 'bar', 'hello');

		foreach ($samples as $poolItem)
		{
			$cacheInstance->save($poolItem);
		}

		$results = $cacheInstance->getItems($keys);
		$this->assertSameSize($samples, $results, __LINE__);
		$this->assertNotSameSize($moreSamples, $results, __LINE__);
		$this->assertNotSameSize($lessSamples, $results, __LINE__);

		/** @var CacheItemInterface $item */
		foreach ($results as $item)
		{
			$itemKey = $item->getKey();
			$itemValue = $item->get();
			$this->assertEquals($itemValue, $expectedSamples[$itemKey], __LINE__);
		}

		// Even if no keys are set, we should still$ have an array of keys
		$badResults = $cacheInstance->getItems($badSampleKeys);
		$this->assertSameSize($badSampleKeys, $badResults, __LINE__);
	}

	/**
	 * Tests the Joomla\Cache\Cache::deleteItems method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::deleteItems
	 * @since   1.0
	 */
	public function testDeleteItems()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bars');

		$stub->method('getKey')
			->willReturn('foo');

		// Create a stub for the CacheItemInterface class.
		$stub2 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub2->method('get')
			->willReturn('google');

		$stub2->method('getKey')
			->willReturn('goo');

		// Create a stub for the CacheItemInterface class.
		$stub3 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub3->method('get')
			->willReturn('world');

		$stub3->method('getKey')
			->willReturn('hello');

		$samples = array($stub, $stub2, $stub3);

		foreach ($samples as $cacheItem)
		{
			$cacheInstance->save($cacheItem);
		}

		$trueSampleKeys = array('foo', 'goo', 'hello');

		$sampleKeys = array_merge(
			$trueSampleKeys,
			array('foobar')
		);
		$results = $cacheInstance->deleteItems($sampleKeys);

		$this->assertTrue($results, "The keys should all be removed even when they do not exist.");
	}

	/**
	 * Tests the Joomla\Cache\Cache::deleteItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::deleteItem
	 * @covers  Joomla\Cache\Memcached::deleteItem
	 * @covers  Joomla\Cache\Memcached::connect
	 * @since   1.0
	 */
	public function testDeleteItem()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bars');

		$stub->method('getKey')
			->willReturn('foo2');

		// Create a stub for the CacheItemInterface class.
		$stub2 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub2->method('get')
			->willReturn('google');

		$stub2->method('getKey')
			->willReturn('goo2');

		// Create a stub for the CacheItemInterface class.
		$stub3 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub3->method('get')
			->willReturn('world');

		$stub3->method('getKey')
			->willReturn('hello2');

		$samples = array($stub, $stub2, $stub3);

		foreach ($samples as $cacheItem)
		{
			$cacheInstance->save($cacheItem);
		}

		$getFoo = $cacheInstance->getItem('foo2');
		$this->assertTrue($getFoo->isHit(), __LINE__);
		$removeFoo = $cacheInstance->deleteItem('foo2');
		$this->assertTrue($removeFoo, __LINE__);
		$removeFoobar = $cacheInstance->deleteItem('foobar');
		$this->assertTrue($removeFoobar, __LINE__);
		$getResult = $cacheInstance->getItem('foo2');
		$this->assertFalse($getResult->isHit(), __LINE__);
	}

	/**
	 * Tests the Joomla\Cache\Cache::setOption method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::getOption
	 * @covers  Joomla\Cache\Cache::setOption
	 * @since   1.0
	 */
	public function testSetOption()
	{
		$cacheInstance = $this->instance;
		$this->assertSame($cacheInstance, $cacheInstance->setOption('foo', 'bar'), 'Checks chaining');
		$this->assertEquals('bar', $cacheInstance->getOption('foo'));
	}

	/**
	 * Tests the Joomla\Cache\Cache::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\Cache::hasItem
	 * @covers  Joomla\Cache\Memcached::hasItem
	 * @since   1.1.3
	 */
	public function testHasItem()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$this->assertFalse(
			$cacheInstance->hasItem('foobar'),
			__LINE__
		);

		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('barfoo');

		$stub->method('getKey')
			->willReturn('foobar');

		$this->assertTrue(
			$cacheInstance->save($stub),
			__LINE__
		);

		$this->assertTrue(
			$cacheInstance->hasItem('foobar'),
			__LINE__
		);
	}

	/**
	 * Tests for the correct Psr\Cache return values.
	 *
	 * @return  void
	 *
	 * @coversNothing
	 * @since   1.0
	 */
	public function testPsrCache()
	{
		$cacheInstance = $this->instance;
		$cacheClass = get_class($cacheInstance);
		$interfaces = class_implements($cacheClass);
		$psrInterface = 'Psr\\Cache\\CacheItemPoolInterface';
		$targetClass = $this->cacheClass;
		$this->assertArrayHasKey($psrInterface, $interfaces, __LINE__);
		$cacheClass = get_class($cacheInstance);
		$this->assertEquals($cacheClass, $targetClass, __LINE__);

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertInternalType('boolean', $cacheInstance->clear(), 'Checking clear.');
		$this->assertInternalType('boolean', $cacheInstance->save($stub), 'Checking save.');
		$this->assertInternalType('string', $cacheInstance->getItem('foo')->get(), 'Checking get.');
		$this->assertInternalType('\Psr\Cache\CacheItemInterface', $cacheInstance->getItem('foo'), 'Checking getItem.');
		$this->assertInternalType('boolean', $cacheInstance->deleteItem('foo'), 'Checking deleteItem.');
		$this->assertInternalType('array', $cacheInstance->getItems(array('foo')), 'Checking getItems.');
		$this->assertInternalType('boolean', $cacheInstance->deleteItems(array('foo')), 'Checking deleteItems.');
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
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Joomla\\Cache\\Item\\AbstractItem')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$expireDate = new \DateTime;
		$expireDate->setTimestamp(time() - 1);
		$stub->method('getExpiration')
			->willReturn($expireDate);

		$this->assertTrue(
			$this->instance->save($stub),
			'Should store the data properly'
		);

		sleep(2);

		$this->assertFalse(
			$this->instance->getItem('foo')->isHit(),
			'Checks expired get.'
		);
	}

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$options = $this->cacheOptions;
		$className = $this->cacheClass;

		if (!$className::isSupported())
		{
			$this->markTestSkipped(sprintf('Cache Handler Class %sis not supported on this system.', $className));
		}

		$cacheInstance = new $className($options);

		$this->instance =& $cacheInstance;

		parent::setUp();
	}

	/**
	 * Teardown the test.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function tearDown()
	{
		if ($this->instance)
		{
			$this->instance->clear();
		}
	}
}
