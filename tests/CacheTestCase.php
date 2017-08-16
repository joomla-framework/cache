<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache\Item\AbstractItem;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;

/**
 * Abstract test case for Joomla! CacheItemPool objects
 */
abstract class CacheTestCase extends TestCase
{
	/**
	 * @var  \Joomla\Cache\AbstractCacheItemPool
	 */
	protected $instance;

	/**
	 * @var  array
	 */
	protected $cacheOptions = ['foo' => 900];

	/**
	 * Tests the registry options are correctly initialised.
	 */
	public function test__construct()
	{
		$this->assertEquals('900', $this->instance->getOption('foo'));
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::clear method.
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
	 * Tests the the Joomla\Cache\AbstractCacheItemPool::getItem method.
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
	 * Tests the the Joomla\Cache\AbstractCacheItemPool::get method.
	 */
	public function testGet()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$cacheInstance->set('foo', 'bar');
		$this->simpleHitKey('foo', 'bar');
		$this->simpleMissKey('foobar', 'foobar');
	}

	/**
	 * Checks to ensure a that $key is not set at all in the Cache
	 *
	 * @param   string  $key    Key of cache item to check
	 * @param   string  $value  Value cache item should be
	 *
	 * @return  void
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
	 * Checks to ensure a that $key is not set at all in the Cache
	 *
	 * @param   string  $key    Key of cache item to check
	 * @param   string  $value  Value cache item should be
	 *
	 * @return  void
	 */
	protected function simpleMissKey($key = '', $value = '')
	{
		$cacheInstance = $this->instance;
		$cacheValue = $cacheInstance->get($key);
		$this->assertNull($cacheValue,  __LINE__);
	}

	/**
	 * Checks to ensure a that $key is set to $value in the Cache
	 *
	 * @param   string  $key    Key of cache item to check
	 * @param   string  $value  Value cache item should be
	 *
	 * @return  void
	 */
	protected function simpleHitKey($key = '', $value = '')
	{
		$cacheInstance = $this->instance;
		$cacheValue = $cacheInstance->get($key, $value);
		$this->assertThat($cacheValue, $this->equalTo($value), __LINE__);
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::save method.
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
	 * Tests the Joomla\Cache\AbstractCacheItemPool::set method.
	 */
	public function testSet()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$this->assertTrue(
			$cacheInstance->set('fooSet', 'barSet'),
			'Set should return true for a valid item'
		);

		$fooValue = $cacheInstance->get('fooSet');
		$this->assertThat($fooValue, $this->equalTo('barSet'), __LINE__);
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::setMultiple method.
	 */
	public function testSetMultiple()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$result = $cacheInstance->setMultiple(['key0' => 'value0', 'key1' => 'value1']);
		$this->assertTrue($result, 'setMultiple() must return true if success');
		$this->assertEquals('value0', $cacheInstance->get('key0'));
		$this->assertEquals('value1', $cacheInstance->get('key1'));
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::getItems method.
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

		// Even if no keys are set, we should still have an array of keys
		$badResults = $cacheInstance->getItems($badSampleKeys);
		$this->assertSameSize($badSampleKeys, $badResults, __LINE__);
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::getMultiple method.
	 */
	public function testGetMultiple()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$result = $cacheInstance->getMultiple(['key0', 'key1']);
		$keys   = [];

		foreach ($result as $i => $r)
		{
			$keys[] = $i;
			$this->assertNull($r);
		}

		sort($keys);

		$this->assertSame(['key0', 'key1'], $keys);
		$cacheInstance->set('key3', 'value');

		$result = $cacheInstance->getMultiple(['key2', 'key3', 'key4'], 'foo');
		$keys   = [];

		foreach ($result as $key => $r)
		{
			$keys[] = $key;

			if ($key === 'key3')
			{
				$this->assertEquals('value', $r);
			}
			else
			{
				$this->assertEquals('foo', $r);
			}
		}

		sort($keys);

		$this->assertSame(['key2', 'key3', 'key4'], $keys);
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::deleteItems method.
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
	 * Tests the Joomla\Cache\AbstractCacheItemPool::deleteItem method.
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
	 * Tests the Joomla\Cache\AbstractCacheItemPool::deleteMultiplemethod.
	 */
	public function testDeleteMultiple()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$sampleData = ['key0' => 'value0', 'key1' => 'value1'];

		$cacheInstance->setMultiple($sampleData);

		$sampleKeys = array_merge(
			array_keys($sampleData),
			['foobar']
		);

		$this->assertTrue($cacheInstance->deleteItems($sampleKeys), "The keys should all be removed even when they do not exist.");
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::delete method.
	 */
	public function testDelete()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$samples = array('foo1' => 'bar1', 'foo2' => 'bar2', 'foo3' => 'bar3');

		foreach ($samples as $key => $value)
		{
			$cacheInstance->set($key, $value);
		}

		$this->assertNotNull($cacheInstance->get('foo2'));
		$this->assertTrue($cacheInstance->delete('foo2'));
		$this->assertTrue($cacheInstance->delete('foobar'));
		$this->assertNull($cacheInstance->get('foo2'));
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::setOption method.
	 */
	public function testSetOption()
	{
		$cacheInstance = $this->instance;
		$this->assertSame($cacheInstance, $cacheInstance->setOption('foo', 'bar'), 'Checks chaining');
		$this->assertEquals('bar', $cacheInstance->getOption('foo'));
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::hasItem method.
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
	 * Tests the Joomla\Cache\AbstractCacheItemPool::has method.
	 */
	public function testHas()
	{
		$cacheInstance = $this->instance;
		$cacheInstance->clear();

		$this->assertFalse(
			$cacheInstance->has('foobar'),
			__LINE__
		);

		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('barfoo');

		$stub->method('getKey')
			->willReturn('foobar');

		$this->assertTrue(
			$cacheInstance->set('foobar', 'barfoo'),
			__LINE__
		);

		$this->assertTrue(
			$cacheInstance->has('foobar'),
			__LINE__
		);
	}

	/**
	 * Tests for the correct Psr\Cache return values.
	 *
	 * @coversNothing
	 */
	public function testPsrCache()
	{
		$cacheInstance = $this->instance;
		$cacheClass = get_class($cacheInstance);
		$interfaces = class_implements($cacheClass);
		$psrInterface = 'Psr\\Cache\\CacheItemPoolInterface';
		$this->assertArrayHasKey($psrInterface, $interfaces, __LINE__);

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
		$this->assertInstanceOf('\Psr\Cache\CacheItemInterface', $cacheInstance->getItem('foo'), 'Checking getItem.');
		$this->assertInternalType('boolean', $cacheInstance->deleteItem('foo'), 'Checking deleteItem.');
		$this->assertInternalType('array', $cacheInstance->getItems(array('foo')), 'Checking getItems.');
		$this->assertInternalType('boolean', $cacheInstance->deleteItems(array('foo')), 'Checking deleteItems.');
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::getItem and Joomla\Cache\AbstractCacheItemPool::save methods with timeout
	 */
	public function testGetAndSaveWithTimeout()
	{
		$stub = $this->getMockForAbstractClass(AbstractItem::class);

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
	 * Tests the Joomla\Cache\AbstractCacheItemPool::saveDeferred method.
	 */
	public function testSaveDeferred()
	{
		$cacheInstance = $this->instance;

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('barDeferred');

		$stub->method('getKey')
			->willReturn('fooDeferred');

		$this->assertTrue(
			$cacheInstance->saveDeferred($stub),
			'Save deferred should return true for a valid item'
		);

		$this->assertEquals(
			array('fooDeferred' => $stub),
			TestHelper::getValue($this->instance, 'deferred')
		);
	}

	/**
	 * Tests the Joomla\Cache\AbstractCacheItemPool::commit method.
	 */
	public function testCommit()
	{
		$stubKey = 'fooCommit';
		$this->assertFalse($this->instance->hasItem($stubKey), 'Item should not exist at test start');

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('barCommit');

		$stub->method('getKey')
			->willReturn($stubKey);

		TestHelper::setValue($this->instance, 'deferred', array($stubKey => $stub));

		$this->assertTrue($this->instance->commit(), 'Commit should return boolean true as successful');

		$this->assertTrue($this->instance->hasItem($stubKey), 'Item should exist in storage');
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		if ($this->instance)
		{
			$this->instance->clear();
		}

		parent::tearDown();
	}
}
