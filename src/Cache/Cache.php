<?php

namespace App\Cache;

class Cache
{
    protected \Redis $redis;

    public function __construct()
    {
        $config = include(__DIR__.'/../../config/redis.php');
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
    }

    public static function get(string $key)
    {
        return (new static())->redis->get($key);
    }

}