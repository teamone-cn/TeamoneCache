<?php

namespace Teamone\Cache;

use Redis;

class RedisCachePsr extends RedisCache implements CachePsr
{
    public function get(string $key, $default = null)
    {
        $value = parent::get($key);

        return is_null($value) ? $default : $value;
    }

    /**
     * @desc
     * @param string $key
     * @param $value
     * @param int|null $ttl
     * @return bool
     */
    public function set(string $key, $value, int $ttl = null): bool
    {
        $ttl = $ttl ?? 0;
        return parent::put($key, $value, $ttl);
    }

    /**
     * @desc
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->forget($key);
    }

    /**
     * @desc
     * @return bool
     */
    public function clear(): bool
    {
        return $this->flush();
    }

    /**
     * @desc
     * @param iterable $keys
     * @param $default
     * @return iterable|mixed[]
     */
    public function getMultiple(iterable $keys, $default = null): iterable
    {
        $arrayKeys = [];
        foreach ($keys as $index => $key) {
            $arrayKeys[$index] = $key;
        }

        return parent::many($arrayKeys);
    }

    /**
     * @desc
     * @param iterable $values
     * @param int|null $ttl
     * @return bool
     */
    public function setMultiple(iterable $values, int $ttl = null): bool
    {
        $ttl = $ttl ?? 0;

        $arrayValues = [];
        foreach ($values as $key => $value) {
            $arrayValues[$key] = $value;
        }

        return parent::putMany($arrayValues, $ttl);
    }

    /**
     * @desc
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $serializedKeys = [];

        foreach ($keys as $index => $key) {
            $serializedKeys[$index] = $this->wrapKey($key);
        }

        $connect = $this->connection();

        /** @var Redis $redis */
        $redis = $connect->multi();

        foreach ($serializedKeys as $key) {
            $redis->del($key);
        }

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
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->connection()->exists($key);
    }

}
