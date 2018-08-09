<?php
namespace Service\User;

/**
 * 注册业务
 * Class RegisterModel
 * @package Service\User
 */
class RegisterModel extends AbstractModel
{
    private static $_instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * 注册
     */
    public function register(array $params)
    {

    }

}
