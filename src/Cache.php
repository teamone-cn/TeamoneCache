<?php

namespace Teamone\Cache;

interface Cache
{
    /**
     * 获取元素
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * 获取多个元素
     *
     * @param array $keys
     * @return array
     */
    public function many(array $keys);

    /**
     * 添加元素
     *
     * @param string $key
     * @param mixed $value
     * @param int $seconds
     * @return bool
     */
    public function put(string $key, $value, int $seconds): bool;

    /**
     * 添加多个元素
     *
     * @param array $values
     * @param int $seconds
     * @return bool
     */
    public function putMany(array $values, int $seconds): bool;

    /**
     * 递增
     *
     * @param string $key
     * @param int $value
     * @return int
     */
    public function increment(string $key, int $value = 1): int;

    /**
     * 递减
     *
     * @param string $key
     * @param int $value
     * @return int
     */
    public function decrement(string $key, int $value = 1): int;

    /**
     * 无限期存储
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function forever(string $key, $value): bool;

    /**
     * 删除元素
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool;

    /**
     * 刷新
     *
     * @return bool
     */
    public function flush(): bool;

    /**
     * 获取前缀
     *
     * @return string
     */
    public function getPrefix(): string;
}

