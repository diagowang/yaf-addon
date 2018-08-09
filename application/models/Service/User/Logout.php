<?php
namespace Service\User;
use Our\Util\Cookie;

/**
 * User: JasonWong
 * Date: 2016/6/17 0017
 * Time: 15:27
 */
class LogoutModel extends AbstractModel
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
     * 注销
     */
    public function logout(){
        if (isset($_SESSION[SESSION_USER_INFO_KEY]['username'])) {
            Cookie::init();
            //清除自动登录的cookie
            $flag = Cookie::get('AUTO_LOGIN');
            if ($flag) {
                Cookie::set('AUTO_LOGIN', '');
            }
        }
    }
}
