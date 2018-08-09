<?php
namespace DAO;


/**
 * User: JasonWong
 * Date: 2016/6/17 0017
 * Time: 14:44
 */
class LocalhostModel extends AbstractModel
{
    const MASTER_DATABASE_KEY = 'LOCALHOST';
    const SLAVE_DATABASE_KEY = 'LOCALHOST';
    protected static $_masterDbModel = null;
    protected static $_slaveDbModel = null;
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
    public function test()
    {
        return $this->getMasterDbModel()->executeSql('select * from user');
    }


}