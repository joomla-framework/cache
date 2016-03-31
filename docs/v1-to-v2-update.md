## Updating from v1 to v2

The following changes were made to the Cache package between v1 and v2.

### PHP 5.3 support dropped

The Cache package now requires PHP 5.4 or newer.

### PSR-6 Changes
#### CachePoolInterface

Version 1 of the cache package used an early version of the PSR-6 package.
This version of the cache package uses the finalised version this means the
following changes have occurred:

1. The interface `Psr\Cache\CacheInterface` is now  `Psr\Cache\CacheItemPoolInterface`

The following methods have a 1:1 mapping:
  * `public function get($key);` has been renamed to `public function getItem($key);`
  * `public function getMultiple($keys);` has been renamed to `public function getItems(array $keys = array());`
  * `public function remove($key);` has been renamed to `public function deleteItem($key);`
  * `public function removeMultiple($keys);` has been renamed to `public function deleteItems(array $keys);`

Furthermore the method `public function set($key, $val, $ttl = null);` has been completely changed - it is now
a save method that . There is also the option to defer the saving of cache items with the
`public function saveDeferred(CacheItemInterface $item);` method to save a deferred item and then using
`public function commit();` to save the deferred items.

The `public function setMultiple($items, $ttl = null);` method has been completely removed

2. The interface `Psr\Cache\CacheItemInterface` has changed:

The following methods have a 1:1 mapping:
  * `public function getValue();` has been renamed to `public function get();`
  * `public function setValue();` has been renamed to `public function set();`

3. Setting expiration of cache items is now done at the Cache item level rather than the cache adapter level.

### APCu Support Added
A handler natively supporting APCu was added.
