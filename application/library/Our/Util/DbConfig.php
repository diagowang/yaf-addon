<?php
namespace Our\Util;
class DbConfig
{
    /**
     * 获取数据库连接信息
     * @param string $key
     * @return mixed
     */
    public static function getDatabaseConfig(string $key)
    {
        static $dbConfig = null;
        if ($dbConfig === null) {
            $dbConfig = include APPLICATION_PATH . '/conf/db.config.php';
        }
        return $dbConfig[$key];
    }
    /**
     * 获取数据库从库（只读库）连接信息
     * @param string $key
     * @return mixed
     */
    public static function getDatabaseSlaveConfig(string $key)
    {
        static $dbConfig = null;
        if ($dbConfig === null) {
            $dbConfig = include APPLICATION_PATH . '/conf/db.slave.config.php';
        }
        return $dbConfig[$key];
    }

}