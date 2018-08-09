<?php
namespace DAO;
use Mysql\DbFactoryModel;

/**
 * 数据读取模型抽象类
 */
abstract class AbstractModel
{
    const MASTER_DATABASE_KEY = '';
    const SLAVE_DATABASE_KEY = '';
    protected static $_masterDbModel = null;
    protected static $_slaveDbModel = null;
    abstract static public function getInstance();
    public function __clone()
    {
        trigger_error('Clone is not allowed!', E_USER_ERROR);
    }

    /**
     * 获取主库的数据库适配器
     * @return \Mysql\AbstractModel
     */
    protected function getMasterDbModel()
    {
        if (!static::$_masterDbModel || !(static::$_masterDbModel instanceof \Mysql\AbstractModel)) {
            $dbFactoryModel = DbFactoryModel::getInstance();
            static::$_masterDbModel = $dbFactoryModel->getDbModel(static::MASTER_DATABASE_KEY);
        }
        return static::$_masterDbModel;
    }

    /**
     * 获取从库的数据库适配器
     * @return \Mysql\Slave\AbstractModel
     */
    protected function getSlaveDbModel()
    {
        if (YAF_ENVIRON != 'product') {
            return $this->getMasterDbModel();
        }
        if (!static::$_slaveDbModel || !(static::$_slaveDbModel instanceof \Mysql\Slave\AbstractModel)) {
            $dbFactoryModel = \Mysql\Slave\DbFactoryModel::getInstance();
            static::$_slaveDbModel = $dbFactoryModel->getDbModel(static::SLAVE_DATABASE_KEY);
        }
        return static::$_slaveDbModel;
    }

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $this->getMasterDbModel()->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $this->getMasterDbModel()->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback()
    {
        $this->getMasterDbModel()->rollback();
    }

}