## Options and General Usage

## Cache Storage Types

The following storage types are supported.

### Apc

```php
use Joomla\Cache\Apc;

$cache = new Apc;
```

### File

The **File** cache allows the following additional options:

* file.path - the path where the cache files are to be stored.
* file.locking

```php
use Joomla\Cache\File;

$options = array(
	'file.path' => __DIR__ . '/cache',
);

$cache = new File($options);
```

### Memcached

```php
use Joomla\Cache\Memcached;

$cache = new Memcached;
```

### None

```php
use Joomla\Cache\None;

$cache = new Cache;
```

### Runtime

```php
use Joomla\Cache\Runtime;

$cache = new Runtime;
```

### Wincache

```php
use Joomla\Cache\Wincache;

$cache = new Wincache;
```

### XCache

```php
use Joomla\Cache\XCache;

$cache = new XCache;
```

## Test Mocking

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
