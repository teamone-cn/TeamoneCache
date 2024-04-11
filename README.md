# Teamone Cache

RedisCache 是一个基于 Redis 的缓存组件，用于在 PHP 应用程序中进行数据缓存。
该组件提供了一系列方法来方便地存储、获取、删除缓存数据，并支持设置缓存过期时间、递增、递减操作等功能。

## 主要特性：

1. 灵活的缓存管理：通过 RedisCache 组件，可以轻松地在应用程序中管理缓存数据，包括设置缓存、获取缓存、删除缓存等操作。
2. 多实例支持：RedisCache 支持同时管理多个 Redis 实例的缓存数据，通过配置不同的连接实例名称，在应用程序中灵活切换不同的 Redis 实例。
3. 多项操作支持：提供了一次性操作多个缓存项的方法，可以有效地减少与 Redis 的交互次数，提升性能。
4. 前缀支持：支持为缓存数据设置前缀，可以在一个 Redis 实例中管理多个缓存数据集合，避免键名冲突。

## 接入指南

````php
public function test(): void
{
    $configs = [
        "default" => [
            'driver'         => RedisConnector::class,
            'host'           => 'redis.jukit.loc',
            'port'           => 6379,
            'timeout'        => 3.0,
            'retry_interval' => 1000, // 重试间隔，单位为毫秒。
            'read_timeout'   => 0,
            'username'       => null,
            'password'       => '123456',
            'database'       => 0,
            'prefix'         => 'default:',
            'name'           => 'Redis',
            'wait_timeout'   => 5, // 连接失败时，等待多久时间重新连接
        ],
        "queue"   => [
            'driver'         => RedisConnector::class,
            'host'           => 'redis.jukit.loc',
            'port'           => 6379,
            'timeout'        => 3.0,
            'retry_interval' => 1000, // 重试间隔，单位为毫秒。
            'read_timeout'   => 0,
            'username'       => null,
            'password'       => '123456',
            'database'       => 1,
            'prefix'         => 'queue:',
            'name'           => 'Redis',
            'wait_timeout'   => 5, // 连接失败时，等待多久时间重新连接
        ],
        "cache"   => [
            'driver'         => RedisConnector::class,
            'host'           => 'redis.jukit.loc',
            'port'           => 6379,
            'timeout'        => 3.0,
            'retry_interval' => 1000, // 重试间隔，单位为毫秒。
            'read_timeout'   => 0,
            'username'       => null,
            'password'       => '123456',
            'database'       => 0,
            'prefix'         => 'cache:',
            'name'           => 'Redis',
            'wait_timeout'   => 5, // 连接失败时，等待多久时间重新连接
        ],
    ];

    $manager = new RedisManager($configs);

    $redisCache = new RedisCachePsr($manager, "cache", "");

    $redisCache = $redisCache;

    $result = $redisCache->redisCache->put("k1", null, 60);
    var_dump($result);

    $result = $redisCache->redisCache->put("k2", "", 60);
    var_dump($result);

    $result = $redisCache->redisCache->put("k3", "v3", 60);
    var_dump($result);
}
````

## 单元测试

````shell
./vendor/bin/phpunit ./test/RedisCacheTest.php --filter testPut$
````

