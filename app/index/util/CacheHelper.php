<?php
/**
 */

namespace app\index\util;

use think\facade\Cache;

class CacheHelper
{
    protected  $_redis;

    public function __construct()
    {
        $this->_redis = Cache::handler();
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->_redis->get($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function del($key)
    {
        return $this->_redis->del($key);
    }

    /**
     * @param $key
     * @param $val
     * @param $expire
     */
    public function setnx($key, $val, $expire = 0)
    {
        $this->_redis->setnx($key, $val);
        if ($expire) {
            $this->_redis->expire($key, $expire);
        }
    }

    /**
     * sadd
     * @param string $key
     * @param string $val
     */
    public function sadd($key, $val)
    {
        return $this->_redis->sadd($key, $val);
    }

    /**
     * sismember
     * @param string $key
     * @param string $val
     */
    public  function sismember($key, $val)
    {
        return $this->_redis->sismember($key, $val);
    }

    /**
     * @param $key
     * @param $val
     * @return mixed
     */
    public function lpush($key, $val)
    {
        return $this->_redis->lpush($key, $val);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function rpop($key)
    {
        return $this->_redis->rpop($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function lpop($key)
    {
        return $this->_redis->lpop($key);
    }
}
