<?php

namespace Teamone\CacheTest;

use PHPUnit\Framework\TestCase;
use Teamone\Cache\RedisCachePsr;
use Teamone\Redis\RedisConnector;
use Teamone\Redis\RedisManager;

class RedisCacheTest extends TestCase
{
    /**
     * @var RedisCachePsr
     */
    private $redisCache;

    public function setUp(): void
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

        $this->redisCache = $redisCache;
    }

    public function testPut()
    {
        $result = $this->redisCache->put("k1", null, 60);
        dump($result);
        $this->assertTrue($result);

        $result = $this->redisCache->put("k2", "", 60);
        dump($result);
        $this->assertTrue($result);

        $result = $this->redisCache->put("k3", "v3", 60);
        dump($result);
        $this->assertTrue($result);
    }

    public function testGet()
    {
        $result = $this->redisCache->get("k1");
        dump($result);
        $this->assertNull($result);

        $result = $this->redisCache->get("k2");
        dump($result);
        $this->assertEquals($result, "");

        $result = $this->redisCache->get("k3");
        dump($result);

        $this->assertEquals($result, 'v3');
    }

    public function testMany()
    {
        $result = $this->redisCache->many(["k1", "k2", "k3"]);
        dump($result);

        $this->assertTrue(true);
    }

    public function testPutMany()
    {
        $result = $this->redisCache->putMany([
            "k1" => "v1",
            "k2" => "v2",
            "k3" => "v3",
        ], 60);
        dump($result);

        $this->assertTrue($result);
    }

    public function testStoreSetup1()
    {
        $result = $this->redisCache->put("book", "经济学", 300);
        dump($result);

        $this->assertTrue($result);
    }

    public function testStoreSetup2()
    {
        $result = $this->redisCache->forever("book", "经济学");
        dump($result);

        $this->assertTrue($result);
    }

    public function testStoreSetup3()
    {
        $result = $this->redisCache->forget("book");
        dump($result);

        $this->assertTrue($result);
    }

    public function testStoreSetup4()
    {
        $result = $this->redisCache->flush();
        dump($result);

        $this->assertTrue($result);
    }

    public function testIncrement()
    {
        $result = $this->redisCache->increment("money");
        dump($result);

        $this->assertIsInt($result);
    }

    public function testDecrement()
    {
        $result = $this->redisCache->decrement("money");
        dump($result);

        $this->assertIsInt($result);
    }
}
