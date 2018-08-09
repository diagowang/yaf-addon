<?php
/**
 * User: JasonWong
 * Date: 2016/8/3 0003
 * Time: 10:17
 */

namespace Redis;

/**
 * redis操作类
 */
class AbstractModel
{

    /**
     * 表名和键的分割符号
     */
    const DELIMITER = '-';

    /**
     * 连接的库
     * @var int
     */
    protected static $_db = 0;

    /**
     * 前缀
     * @var string
     */
    static $prefix = "";

    /**
     * redis连接对象，未选择库的
     * @var \Redis
     */
    static $redis;

    /**
     * 获取redis连接
     * @return \Redis
     * @throws \Exception
     */
    public static function getRedis()
    {
        if (!self::$redis) {
            $conf = \Yaf_Registry::get('config')->get('redis.database.params');
            if (!$conf) {
                throw new \Exception('redis连接必须设置');
            }
            if (isset($conf['prefix'])) {
                self::$prefix = $conf['prefix'];
            }

            self::$redis = new \Redis();
            self::$redis->connect($conf['host'], $conf['port']);

            if (!empty($conf['password'])) {
                self::$redis->auth($conf['password']);
            }
        }
        self::$redis->select(static::$_db);
        return self::$redis;
    }

    /**
     * 给key增加前缀
     * @param string $key
     * @return string
     */
    private function _addPrefix($key)
    {
        if (self::$prefix) {
            return self::$prefix . self::DELIMITER . $key;
        }
        return $key;
    }

    /**
     * 删除key
     * @param string $key
     * @return int
     */
    public function del(string $key)
    {
        return $this->getRedis()->del($this->_addPrefix($key));
    }

    /**
     * 获取keys
     * @param $pattern
     * @return array
     */
    public function keys($pattern)
    {
        return $this->getRedis()->keys($pattern);
    }

    /**
     * 增加缓存
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value, $second = '')
    {
        if($second){
            return $this->getRedis()->set($this->_addPrefix($key), $value, $second);
        } else {
            return $this->getRedis()->set($this->_addPrefix($key), $value);
        }
    }

    /**
     * 根据key值获取缓存数据
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->getRedis()->get($this->_addPrefix($key));
    }

    /**
     * redis自增1
     *
     * @param string $key
     * @return int
     */
    public function incr($key)
    {
        return $this->getRedis()->incr($this->_addPrefix($key));
    }

    /**
     * redis自减1
     *
     * @param string $key
     * @return int
     */
    public function decr($key)
    {
        return $this->getRedis()->decr($this->_addPrefix($key));
    }

    /**
     * redis自减1
     * @param $key
     * @param $decrement
     * @return int
     */
    public function decrby($key, $decrement)
    {
        return $this->getRedis()->decrby($this->_addPrefix($key), $decrement);
    }

    /**
     * 增加列表内的元素
     * @param string $key
     * @param mix $value
     * @return int
     */
    public function lpush($key, $value)
    {
        return $this->getRedis()->lpush($this->_addPrefix($key), $value);
    }

    /**
     * 获取列表内的元素
     *
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return mix
     */
    public function lrange($key, $start, $stop)
    {
        return $this->getRedis()->lrange($this->_addPrefix($key), $start, $stop);
    }

    /**
     * 增加集合内的元素
     *
     * @param string $key
     * @param mix $value
     * @return int
     */
    public function sadd($key, $value)
    {
        return $this->getRedis()->sadd($this->_addPrefix($key), $value);
    }

    /**
     * 列出集合内的元素
     *
     * @param int $key
     * @return mix
     */
    public function smembers($key)
    {
        return $this->getRedis()->smembers($this->_addPrefix($key));
    }

}