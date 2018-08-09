<?php
namespace Our\Util;
class OurSessionHandler implements \SessionHandlerInterface
{
    protected $_redis;
    protected $_name;
    protected $_sessionid;
    protected $_db = 4;

    public function close()
    {
        return true;
    }

    public function destroy($session_id)
    {
        $key = $this->_name.$session_id;
        $this->_redis->del($key);
        return true;
    }

    public function gc($maxlifetime)
    {
        $key = $this->_name.$this->_sessionid;
        $this->_redis->del($key);
    }

    public function open($save_path, $name)
    {
        $conf = \Yaf_Registry::get('config')->get('redis.database.params');
        $this->_redis = new \Redis();
        $this->_redis->connect($conf['host'], $conf['port']);
        $this->_redis->select($this->_db);
        $this->_name = $name.':';
        return true;
    }

    public function read($session_id)
    {
        $this->_sessionid = $session_id;
        $key = $this->_name.$session_id;
        $data = $this->_redis->get($key);
        if(!$data){
            $_SESSION = array();
            return '';
        }
        $_SESSION = json_decode($data, true);
        return '';
    }

    public function write($session_id, $session_data)
    {
        $key = $this->_name.$session_id;
        $this->_redis->set($key, json_encode($_SESSION), intval(ini_get('session.gc_maxlifetime')));
        return true;
    }

}
