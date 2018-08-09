<?php

namespace Mysql\Slave;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;

abstract class AbstractModel implements InterfaceModel
{
    const EXECUTE_SQL_INSERT = 1;
    const EXECUTE_SQL_UPDATE = 2;
    const EXECUTE_SQL_DELETE = 3;
    const EXECUTE_SQL_FETCH = 4;
    const EXECUTE_SQL_FETCH_ALL = 5;
    protected $_databaseKey = '';
    protected $_maxFetchCount = 100;//查询时的最大行数
    protected $_dbAdapter = null;//数据库适配器对象
    protected $_dbConnection = null;//数据库连接对象
    protected $_fetchMode = \PDO::FETCH_ASSOC;//查询数据库返回值类型，默认是索引数组

    /**
     * 获取数据库适配器
     * @return Capsule|\PDO
     */
    public function getDbAdapter()
    {
        return $this->_dbAdapter;
    }

    /**
     * 获取数据库连接
     * @return Connection
     */
    public function getDbConnection()
    {
        return $this->_dbConnection;
    }

    /**
     * @return int
     */
    public function getMaxFetchCount(): int
    {
        return $this->_maxFetchCount;
    }

    /**
     * @param int $maxFetchCount
     */
    public function setMaxFetchCount(int $maxFetchCount)
    {
        $this->_maxFetchCount = $maxFetchCount;
    }

    /**
     * @return int
     */
    public function getFetchMode(): int
    {
        return $this->_fetchMode;
    }

    /**
     * @param int $fetchMode
     */
    public function setFetchMode(int $fetchMode)
    {
        $this->_fetchMode = $fetchMode;
    }

    /**
     * 简单查询单表数据，仅支持where条件（=，<,>,!=,in,not in,between,not between）和having条件（=，<,>,!=）
     * @param string $tableName
     * @param array|null $columns
     * @param array|null $where
     * @param bool $fetchAll
     * @param array|null $orderBy
     * @param int $count
     * @param int $offset
     * @param array|null $groupBy
     * @param array|null $having
     * @return array
     */
    abstract public function fetchSimpleQueryData(string $tableName, array $columns = null, array $where = null, bool $fetchAll = false, array $orderBy = null, int $count = 1, int $offset = 0, array $groupBy = null, array $having = null);

    /**
     * 获取简单查询结果的数量，仅支持where条件（=，<,>,!=,in,not in,between,not between）和having条件（=，<,>,!=）
     * @param string $tableName
     * @param array $where
     * @param array $groupBy
     * @param array $having
     * @return mixed
     */
    abstract public function getSimpleQueryRowCount(string $tableName, array $where = null, array $groupBy = null, array $having = null);

    /**
     * 执行SQL
     * @param string $sql
     * @param array|null $sqlParameter
     * @param int $type
     * @return array|bool|int|mixed|string
     */
    abstract public function executeSql(string $sql, array $sqlParameter = null, int $type = self::EXECUTE_SQL_FETCH_ALL);
}