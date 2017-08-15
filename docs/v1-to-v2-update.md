## Updating from v1 to v2

The following changes were made to the Cache package between v1 and v2.

### Minimum supported PHP version raised

All framework packages now require PHP 7.0 or newer.

### PSR-6 Changes

#### CachePoolInterface

Version 1 of the Cache package used an early version of the PSR-6 package. This version of the cache package uses the finalised version.
The following changes have occurred:

1) The interface `Psr\Cache\CacheInterface` is now `Psr\Cache\CacheItemPoolInterface`

The following methods have a 1:1 mapping:
  * `get($key)` has been renamed to `getItem($key)`
  * `getMultiple($keys)` has been renamed to `getItems(array $keys = [])`
  * `remove($key)` has been renamed to `deleteItem($key)`
  * `removeMultiple($keys)` has been renamed to `deleteItems(array $keys)`

The method `set()` has been replaced with a `save()` method.

There is also the option to defer the saving of cache items with the `saveDeferred(CacheItemInterface $item)` method to save a deferred
item and then using `commit()` to save the deferred items.

The `setMultiple()` method has been removed, each item should be saved via the `save()` method. 

2) The interface `Psr\Cache\CacheItemInterface` has changed:

The following methods have a 1:1 mapping:
  * `getValue()` has been renamed to `get()`
  * `setValue()` has been renamed to `set()`

3) Setting expiration of cache items is now done at the item level rather than the adapter level.

### UnsupportedFormatException Removed

The `Joomla\Cache\Exception\UnsupportedFormatException` class has been removed. This was previously thrown when an adapter is not
supported on an environment. Instead, a `Joomla\Cache\CacheItemPoolInterface` interface extending the `Psr\Cache\CacheItemPoolInterface`
interface has been added and all CacheItemPool implementations offer a static `isSupported()` method to test for support.

### `Joomla\Cache\Cache` Renamed

The `Joomla\Cache\Cache` class has been renamed to `Joomla\Cache\AbstractCacheItemPool`

### Adapter Namespace

All cache pool adapters are now in the `Joomla\Cache\Adapter` namespace

### APCu Support Added

A handler natively supporting APCu was added.

### Runtime Adapter

The Runtime Adapter now stores its data in a non-static object

### Memcached Adapter

A configured `Memcached` instance must be injected to the Memcached adapter now as its first constructor parameter.

### Redis Adapter

A configured `Redis` instance must be injected to the Redis adapter now as its first constructor parameter.
