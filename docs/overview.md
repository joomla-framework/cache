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
