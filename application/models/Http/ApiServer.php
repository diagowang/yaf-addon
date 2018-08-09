<?php
/**
 * User: JasonWong
 * Date: 2017/12/4 0004
 * Time: 15:37
 */
namespace Http;

use DAO\SysLogsModel;

class ApiServerModel extends AbstractModel
{
    private static $_instance = null;
    private function __construct()
    {
        $this->setHost(\Bootstrap::getUrlIniConfig('apiServerHost'));
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 用户注册
     * @param array $params
     * @return array|bool|mixed
     */
    public function register(array $params)
    {
//        $this->setHost('http://192.168.88.163:98');//临时添加，提交api代码后需删除
        $url = '/user/register';
        return $this->_sendPostRequest($url, $params);
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $sync
     * @param string $returnType
     * @param array $cookie
     * @return array|bool|mixed
     */
    private function _sendPostRequest(string $url, array $data, bool $sync = true, $returnType = 'json',  array $cookie = array())
    {
        // 日志公用参数
        if ($sync) {
            $data = json_encode($data);
            $result = $this->_request($url, 'POST', $data);
        } else {
            $result = $this->_requestAsync($url, $data, $cookie);
        }
        return $result;
    }

}