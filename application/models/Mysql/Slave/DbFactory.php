<?php
/**
 * 创建数据库模型类（单例模式）
 */

namespace Mysql\Slave;
class DbFactoryModel
{
    private static $_instance = null;
    private static $_connections = null;//数据库连接池

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
     * 获取指定数据库模型
     * @param string $databaseKey
     * @return AbstractModel
     */
    public function getDbModel(string $databaseKey): AbstractModel
    {
        if (!$this->_isExistDbModel($databaseKey)) {
            self::$_connections[$databaseKey] = new DbModel($databaseKey);
        }
        return self::$_connections[$databaseKey];
    }

    /**
     * 设置数据库模型
     * @param string $databaseKey
     * @param AbstractModel $dbModel
     */
    public function setDbModel(string $databaseKey, AbstractModel $dbModel)
    {
        if (!$this->_isExistDbModel($databaseKey)) {
            self::$_connections[$databaseKey] = $dbModel;
        }
    }

    /**
     * 检测数据库模型是否存在
     * @param string $databaseKey
     * @return bool
     */
    private function _isExistDbModel(string $databaseKey)
    {
        return isset(self::$_connections[$databaseKey]) && (self::$_connections[$databaseKey] instanceof AbstractModel);
    }
}