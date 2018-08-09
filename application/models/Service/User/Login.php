<?php
namespace Service\User;

/**
 * User: JasonWong
 * Date: 2016/6/17 0017
 * Time: 15:27
 */
class LoginModel extends AbstractModel
{
    private $_sessionId;
    private static $_instance = null;
    private function __construct()
    {
        $this->_sessionId = session_id();
    }
    public static function getInstance ()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 登录
     */
    public function login($params)
    {

    }





}
