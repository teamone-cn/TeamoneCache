<?php

namespace Teamone\Cache;

use Redis;
use Teamone\Redis\Connection;
use Teamone\Redis\Factory;

class RedisCache implements Cache
{
    /**
     * @var \Teamone\Redis\Factory
     */
    protected $redisFactory;

    /**
     * @var string 前缀
     */
    protected $prefix;

    /**
     * @var string 连接实例
     */
    protected $connection;

    public function __construct(Factory $redisFactory, string $connection = 'default', string $prefix = '')
    {
        $this->redisFactory = $redisFactory;
        $this->prefix       = $prefix;
        $this->connection   = $connection;
    }

    /**
     * @desc 获取 Redis 连接实例
     * @return Connection
     */
    protected function connection(): Connection
    {
        return $this->redisFactory->connection($this->connection);
    }

    /**
     * @desc
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $value = $this->connection()->get($this->wrapKey($key));

        $value = unserialize($value, [
            'allowed_classes' => true,
            'max_depth'       => 4096
        ]);

        return $value;
    }

    /**
     * @desc
     * @param array $keys
     * @return mixed
     */
    public function many(array $keys)
    {
        $serializedValues = $this->connection()->mGet(array_map(function ($key) {
            return $this->wrapKey($key);
        }, $keys));

        $values = [];

        foreach ($serializedValues as $key => $value) {
            $values[$key] = unserialize($value, [
                'allowed_classes' => true,
                'max_depth'       => 4096
            ]);
        }

        return $values;
    }

    /**
     * @desc
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     * @return bool
     */
    public function put(string $key, $value, int $seconds): bool
    {
        return $this->connection()->setex(
            $this->wrapKey($key),
            (int)max(1, $seconds),
            serialize($value)
        );
    }

    /**
     * @desc
     * @param array $values
     * @param $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds): bool
    {
        $serializedValues = [];

        foreach ($values as $key => $value) {
            $serializedValues[$this->wrapKey($key)] = serialize($value);
        }

        $connect = $this->connection();

        /** @var Redis $redis */
        $redis = $connect->multi();

        foreach ($serializedValues as $key => $value) {
            $redis->setex(
                $key, (int)max(1, $seconds), $value
            );
        }

        /**
         * $execResult = [
         *    0 => true
         *    1 => true
         *    2 => true
         * ]
         */
        $execResult = $redis->exec();

        foreach ($execResult as $item) {
            if (!$item) {
                return false;
            }
        }

        return true;
    }

    /**
     * @desc
     * @param $key
     * @param $value
     * @return int
     */
    public function increment(string $key, int $value = 1): int
    {
        return $this->connection()->incrBy($this->wrapKey($key), $value);
    }

    /**
     * @desc
     * @param $key
     * @param $value
     * @return mixed
     */
    public function decrement(string $key, int $value = 1): int
    {
        return $this->connection()->decrBy($this->wrapKey($key), $value);
    }

    /**
     * @desc 将项无限期地存储在缓存中。
     * @param string $key
     * @param $value
     * @return bool
     */
    public function forever(string $key, $value): bool
    {
        return $this->connection()->set($this->wrapKey($key), serialize($value));
    }

    /**
     * @desc 从缓存中删除
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return (bool)$this->connection()->del($this->wrapKey($key));
    }

    /**
     * @desc 删除所有数据
     * @return bool
     */
    public function flush(): bool
    {
        return $this->connection()->flushdb();
    }

    /**
     * @desc
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    protected function wrapKey(string $key)
    {
        return $this->prefix . $key;
    }
}
