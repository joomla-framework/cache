<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cache\Tests;

use Joomla\Cache;
use Joomla\Test\TestHelper;

/**
 * Tests for the Joomla\Cache\FileTest class.
 *
 * @since  1.0
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Cache\File
	 * @since  1.0
	 */
	private $instance;

	/**
	 * Tests the Joomla\Cache\File::__construct method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::__construct
	 * @since   1.1.3
	 * @expectedException  \RuntimeException
	 */
	public function test__construct()
	{
		$options = array(
			'file.path' => '/'
		);

		$this->instance = new Cache\File($options);
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
		$this->assertInternalType('boolean', $this->instance->clear(), 'Checking clear.');
		$this->assertInstanceOf('\Psr\Cache\CacheItemInterface', $this->instance->getItem('foo'), 'Checking get.');
		$this->assertInternalType('array', $this->instance->getItems(array('foo')), 'Checking getMultiple.');
		$this->assertInternalType('boolean', $this->instance->deleteItem('foo'), 'Checking remove.');
		$this->assertInternalType('array', $this->instance->deleteItems(array('foo')), 'Checking removeMultiple.');

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertInternalType('boolean', $this->instance->save($stub), 'Checking set.');
	}

	/**
	 * Tests constructor when given non writable cache folder
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::__construct
	 * @since   1.1.4
	 * @expectedException  \RuntimeException
	 */
	public function test__construct_exception()
	{
		$options = array(
			'file.path' => __DIR__ . '/no_write_tmp'
		);

		mkdir($options['file.path']);
		chmod($options['file.path'], 0000);

		new Cache\File($options);
	}

	/**
	 * Tests the Joomla\Cache\File::clear method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::clear
	 * @since   1.0
	 */
	public function testClear()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);

		// Create a stub for the CacheItemInterface class.
		$stub2 = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub2->method('get')
			->willReturn('car');

		$stub2->method('getKey')
			->willReturn('goo');

		$this->instance->save($stub2);

		$this->instance->clear();

		$this->assertFalse($this->instance->getItem('foo')->isHit());
		$this->assertFalse($this->instance->getItem('goo')->isHit());
	}

	/**
	 * Tests the Joomla\Cache\File::get method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::get
	 * @since   1.0
	 */
	public function testGet()
	{
		$this->assertFalse($this->instance->getItem('foo')->isHit(), 'Checks an unknown key.');

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Joomla\\Cache\\Item\\AbstractItem')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$stub->method('getExpiration')
			->willReturn(time() - 2);

		$this->instance->save($stub);

		$this->assertEquals(
			'bar',
			$this->instance->getItem('foo')->get(),
			'The key should have not been deleted.'
		);

		$fileName = TestHelper::invoke($this->instance, 'fetchStreamUri', 'foo');
		touch($fileName, time() -2);

		$this->assertFalse(
			$this->instance->getItem('foo')->isHit(),
			'The key should have been deleted.'
		);
	}

	/**
	 * Tests the Joomla\Cache\File::get method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::get
	 * @expectedException  \RuntimeException
	 * @since   1.1.3
	 */
	public function testGetCantRemoveExpiredKeyException()
	{
		$options = array(
			'file.path' => __DIR__ . '/tmp'
		);

		$instance = $this->getMockBuilder('Joomla\Cache\File');
		$instance = $instance->setMethods(array('deleteItem'));
		$instance = $instance->setConstructorArgs(array($options));
		$instance = $instance->getMock();

		$instance->expects($this->any())
				->method('deleteItem')
				->willReturn(false);

		$instance->setOption('ttl', 1);

		$stub = $this->getMockBuilder('Joomla\Cache\Item\Item');
		$stub = $stub->setMethods(array('get', 'getKey', 'getExpiration'));
		$stub = $stub->setConstructorArgs(array('foo'));
		$stub = $stub->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$stub->method('getExpiration')
			->willReturn(time() + 1);

		$instance->save($stub);

		sleep(2);

		$this->assertNull(
			$instance->getItem('foo')->get(),
			'The key should have been deleted.'
		);
	}

	/**
	 * Tests the Joomla\Cache\File::hasItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::hasItem
	 * @since   1.0
	 */
	public function testHasItem()
	{
		$this->assertFalse($this->instance->hasItem('foo'));

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertTrue($this->instance->hasItem('foo'));
	}

	/**
	 * Tests the Joomla\Cache\File::deleteItem method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::deleteItem
	 * @since   1.0
	 */
	public function testDeleteItem()
	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertTrue(
			$this->instance->save($stub),
			'Checks the value was set'
		);
		$this->assertTrue(
			$this->instance->deleteItem('foo'),
			'Checks the value was removed'
		);
		$this->assertNull(
			$this->instance->getItem('foo')->get(),
			'Checks for the delete'
		);
	}

	/**
	 * Tests the Joomla\Cache\File::deleteItem method fail to remove
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::deleteItem
	 * @since   1.1.4
	 */
	public function testDeleteItemFail()
 	{
		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('barabum');

		$stub->method('getKey')
			->willReturn('foo');

		$this->assertTrue(
			$this->instance->save($stub),
			'Checks the value was set'
		);

		$fileName = TestHelper::invoke($this->instance, 'fetchStreamUri', 'foo');
 		unlink($fileName);

  		$this->assertFalse($this->instance->deleteItem('foo'), 'Checks the value was removed');
 	}

	/**
	 * Tests the Joomla\Cache\File::save method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::save
	 * @covers  Joomla\Cache\File::getItem
	 * @covers  Joomla\Cache\File::deleteItem
	 * @since   1.0
	 * @todo    Custom ttl is not working in set yet.
	 */
	public function testSave()
	{
		$fileName = TestHelper::invoke($this->instance, 'fetchStreamUri', 'foo');

		$this->assertFileNotExists($fileName);

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertFileExists(
			$fileName,
			'Checks the cache file was created.'
		);

		$this->assertEquals(
			'bar', $this->instance->getItem('foo')->get(),
			'Checks we got the cached value back.'
		);

		$this->instance->deleteItem('foo');
		$this->assertNull(
			$this->instance->getItem('foo')->get(),
			'Checks for the delete.'
		);
	}

	/**
	 * Tests the Joomla\Cache\File::checkFilePath method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::checkFilePath
	 * @since   1.0
	 */
	public function testCheckFilePath()
	{
		$this->assertTrue(TestHelper::invoke($this->instance, 'checkFilePath', __DIR__));
	}

	/**
	 * Tests the Joomla\Cache\File::checkFilePath method for a known exception.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Cache\File::checkFilePath
	 * @expectedException  \RuntimeException
	 * @since              1.0
	 */
	public function testCheckFilePathInvalidPath()
	{
		// Invalid path
		TestHelper::invoke($this->instance, 'checkFilePath', 'foo123');
	}

	/**
	 * Tests the Joomla\Cache\File::checkFilePath method for a known exception.
	 *
	 * @return  void
	 *
	 * @covers             Joomla\Cache\File::checkFilePath
	 * @expectedException  \RuntimeException
	 * @since              1.0
	 */
	public function testCheckFilePathUnwritablePath()
	{
		// Check for an unwritable folder.
		mkdir(__DIR__ . '/tmp/~uwd', 0444);
		TestHelper::invoke($this->instance, 'checkFilePath', __DIR__ . '/tmp/~uwd');
	}

	/**
	 * Tests the Joomla\Cache\File::fetchStreamUri method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::fetchStreamUri
	 * @since   1.0
	 */
	public function testFetchStreamUri()
	{
		$fileName = TestHelper::invoke($this->instance, 'fetchStreamUri', 'test');
	}

	/**
	 * Tests the Joomla\Cache\File::isExpired method.
	 *
	 * @return  void
	 *
	 * @covers  Joomla\Cache\File::isExpired
	 * @since   1.0
	 */
	public function testIsExpired()
	{
		$this->instance->setOption('ttl', 1);

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);

		$fileName = TestHelper::invoke($this->instance, 'fetchStreamUri', 'foo');
		touch($fileName, time() -2);

		$this->assertTrue(TestHelper::invoke($this->instance, 'isExpired', 'foo'), 'Should be expired.');

		$this->instance->setOption('ttl', 900);

		// Create a stub for the CacheItemInterface class.
		$stub = $this->getMockBuilder('\\Psr\\Cache\\CacheItemInterface')
			->getMock();

		$stub->method('get')
			->willReturn('bar');

		$stub->method('getKey')
			->willReturn('foo');

		$this->instance->save($stub);
		$this->assertFalse(TestHelper::invoke($this->instance, 'isExpired', 'foo'), 'Should not be expired.');
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
		parent::setUp();

		// Clean up the test folder.
		$this->tearDown();

		$options = array(
			'file.path' => __DIR__ . '/tmp'
		);

		$this->instance = new Cache\File($options);
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
		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator(__DIR__ . '/tmp/'),
			\RecursiveIteratorIterator::CHILD_FIRST
		);

		foreach ($iterator as $file)
		{
			if ($file->isFile() && $file->getExtension() == 'data')
			{
				unlink($file->getRealPath());
			}
			elseif ($file->isDir() && strpos($file->getFilename(), '~') === 0)
			{
				rmdir($file->getRealPath());
			}
		}

		if (is_dir(__DIR__ . '/no_write_tmp'))
		{
			rmdir(__DIR__ . '/no_write_tmp');
		}
	}
}
