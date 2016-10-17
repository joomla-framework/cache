## Overview

The Cache package provides an implementation of the [PSR-6 caching interface](http://www.php-fig.org/psr/psr-6/).

### Options and General Usage

**TODO - Write this section**

### Cache Storage Types

The following storage types are supported.

#### APC

```php
use Joomla\Cache\Adapter\Apc;

$cache = new Apc;
```

#### APCu

```php
use Joomla\Cache\Adapter\Apcu;

$cache = new Apcu;
```

#### Filesystem

The **File** cache allows the following additional options:

* file.path - the path where the cache files are to be stored.
* file.locking

```php
use Joomla\Cache\Adapter\File;

$options = [
	'file.path' => __DIR__ . '/cache',
];

$cache = new File($options);
```

#### Memcached

To use Memcached storage, a configured [Memcached](https://secure.php.net/manual/en/class.memcached.php) instance must be provided.

```php
use Joomla\Cache\Adapter\Memcached as MemcachedAdapter;

$memcached = new \Memcached;

// Configure the instance

$cache = new MemcachedAdapter($memcached);
```

#### None

```php
use Joomla\Cache\Adapter\None;

$cache = new None;
```

#### Redis

To use Redis storage, a configured `Redis` instance must be provided.

```php
use Joomla\Cache\Adapter\Redis as RedisAdapter;

$redis = new \Redis;

// Configure the instance

$cache = new RedisAdapter($redis);
```

#### Runtime

```php
use Joomla\Cache\Adapter\Runtime;

$cache = new Runtime;
```

#### Wincache

```php
use Joomla\Cache\Adapter\Wincache;

$cache = new Wincache;
```

#### XCache

```php
use Joomla\Cache\Adapter\XCache;

$cache = new XCache;
```

### Test Mocking

The `Cache` package provide a **PHPUnit** helper to mock a `Cache\Cache` object or an `Cache\Item` object. You can include your own optional overrides in the test class for the following methods:

* `Cache\Cache::getItem`: Add a method called `mockCacheGetItem` to your test class. If omitted, the helper will return a default mock for the `Cache\Item` class.
* `Cache\Item::get`: Add a method called `mockCacheItemGet` to your test class. If omitted, the mock `Cache\Item` will return `"value"` when this method is called.
* `Cache\Item::isHit`: Add a method called `mockCacheItemIsHit` to your test class. If omitted, the mock `Cache\Item` will return `false` when this method is called.

```php
use Joomla\Cache\Tests\Mocker as CacheMocker;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
	private $instance;

	//
	// The following mocking methods are optional.
	//

	/**
	 * Callback to mock the Cache\Item::getValue method.
	 *
	 * @return  string
	 */
	public function mockCacheItemGet()
	{
		// This is the default handling.
		// You can override this method to provide a custom return value.
		return 'value';
	}

	/**
	 * Callback to mock the Cache\Item::isHit method.
	 *
	 * @return  boolean
	 */
	public function mockCacheItemIsHit()
	{
		// This is the default handling.
		// You can override this method to provide a custom return value.
		return false;
	}

	/**
	 * Callback to mock the Cache\Cache::getItem method.
	 *
	 * @param   string  $text  The input text.
	 *
	 * @return  string
	 */
	public function mockCacheGetItem($key)
	{
		// This is the default handling.
		// You can override this method to provide a custom return value.
		return $this->createMockItem();
	}

	protected function setUp()
	{
		parent::setUp();

		$mocker = new CacheMocker($this);

		$this->instance = new SomeClass($mocker->createMockCache());
	}
}
```
