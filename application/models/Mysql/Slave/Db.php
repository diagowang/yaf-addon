<?php
/**
 * 数据库模型
 */

namespace Mysql\Slave;

use Error\CodeConfigModel;
use Error\ErrorModel;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Database\Query\Builder;
use Illuminate\Events\Dispatcher;
use Our\Util\DbConfig;

class DbModel extends AbstractModel
{
    protected $_databaseKey = '';
    protected $_maxFetchCount = 100;//查询时的最大行数
    protected $_dbAdapter = null;//数据库连接对象
    protected $_fetchMode = \PDO::FETCH_ASSOC;//查询数据库返回值类型，默认是索引数组

    public function __clone()
    {
        trigger_error('Clone is not allowed!', E_USER_ERROR);
    }

    /**
     * DbModel constructor.
     * @param string $databaseKey
     */
    public function __construct(string $databaseKey)
    {
        $this->_databaseKey = $databaseKey;
        $this->_connect();
    }

    /**
     * 连接数据库
     */
    private function _connect()
    {
        try {
            $conf = DbConfig::getDatabaseSlaveConfig($this->_databaseKey);
            $databaseConf = array(
                'driver' => 'mysql',
                'port' => 3306,
                'database' => $conf['db_dbname'],
                'username' => $conf['db_user'],
                'password' => $conf['db_pass']
            );
            $hostList = explode(":", $conf['db_host']);
            if (count($hostList) > 1) {
                $databaseConf['host'] = $hostList[0];
                $databaseConf['port'] = $hostList[1];
            } else {
                $databaseConf['host'] = $conf['db_host'];
            }
            if (isset($conf['charset'])) {
                $databaseConf['charset'] = 'utf8';
            }
            $capsule = new Capsule();
            $capsule->addConnection($databaseConf);
            $capsule->setFetchMode($this->_fetchMode);
            $dispatcher = new Dispatcher();
            $dispatcher->listen(StatementPrepared::class, function ($event) {
                $event->statement->setFetchMode($this->_fetchMode);
            });
            $capsule->setEventDispatcher($dispatcher);
            $this->_dbAdapter = $capsule;
            $this->_dbConnection = $capsule->getConnection();
        } catch (\Exception $e) {
            ErrorModel::throwException(CodeConfigModel::CANNOT_CONNECT_DATABASE, $e->getMessage());
        }
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
     * @return \Illuminate\Support\Collection
     */
    public function fetchSimpleQueryData(string $tableName, array $columns = null, array $where = null, bool $fetchAll = false, array $orderBy = null, int $count = 0, int $offset = 0, array $groupBy = null, array $having = null)
    {
        empty($columns) && ($columns = ['*']);
        $builder = $this->getDbConnection()->table($tableName)->select($columns);
        $this->_processQueryBuilderWhere($builder, $where);
        if ($groupBy && is_array($groupBy)) {
            $builder->groupBy($groupBy);
            if ($having && is_array($having)) {
                foreach ($having as $key => $value) {
                    if (is_array($value)) {
                        if (isset($value['operator'])) {
                            $builder->having($key, $value['operator'], $value);
                        } else {
                            $builder->having($key, $value);
                        }
                    } else {
                        $builder->having($key, $value);
                    }
                }
            }
        }
        if ($orderBy && is_array($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $builder->orderBy($key, $value);
            }
        }
        if ($count) {
            ($count > $this->_maxFetchCount) && ($count = $this->_maxFetchCount);
            $builder->skip(intval($offset))->take(intval($count));
        }
        if ($fetchAll) {
            return $builder->get()->all();
        }
        return $builder->get()->first();
    }

    /**
     * 获取简单查询结果的数量，仅支持where条件（=，<,>,!=,in,not in,between,not between）和having条件（=，<,>,!=）
     * @param string $tableName
     * @param array $where
     * @param array $groupBy
     * @param array $having
     * @return mixed
     */
    public function getSimpleQueryRowCount(string $tableName, array $where = null, array $groupBy = null, array $having = null)
    {
        $builder = $this->getDbConnection()->table($tableName);
        $this->_processQueryBuilderWhere($builder, $where);
        if ($groupBy && is_array($groupBy)) {
            $builder->groupBy($groupBy);
            if ($having && is_array($having)) {
                foreach ($having as $key => $value) {
                    if (is_array($value)) {
                        if (isset($value['operator'])) {
                            $builder->having($key, $value['operator'], $value);
                        } else {
                            $builder->having($key, $value);
                        }
                    } else {
                        $builder->having($key, $value);
                    }
                }
            }
        }
        return $builder->count();
    }

    /**
     * 执行SQL
     * @param string $sql
     * @param array|null $sqlParameter
     * @param int $type
     * @return array|bool|int|mixed|string
     */
    public function executeSql(string $sql, array $sqlParameter = null, int $type = self::EXECUTE_SQL_FETCH_ALL)
    {
        $pdo = $this->getDbConnection()->getPdo();
        $sth = $pdo->prepare($sql);
        $flag = $sth->execute($sqlParameter);
        switch ($type) {
            case self::EXECUTE_SQL_INSERT:
                return $pdo->lastInsertId();
                break;
            case self::EXECUTE_SQL_UPDATE:
                return $sth->rowCount();
                break;
            case self::EXECUTE_SQL_DELETE:
                return $sth->rowCount();
                break;
            case self::EXECUTE_SQL_FETCH:
                return $sth->fetch(\PDO::FETCH_ASSOC);
                break;
            case self::EXECUTE_SQL_FETCH_ALL:
                return $sth->fetchAll(\PDO::FETCH_ASSOC);
                break;
            default:
                return $flag;
                break;
        }
    }

    /**
     * 处理不同方式where条件
     * @param Builder $builder
     * @param array $where
     */
    private function _processQueryBuilderWhere(Builder &$builder, array $where)
    {
        if ($where && is_array($where)) {
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    if (isset($value['operator'])) {
                        $operator = strtolower($value['operator']);
                        switch ($operator) {
                            case 'in':
                                $builder->whereIn($key, $value['value']);
                                break;
                            case 'not in':
                                $builder->whereNotIn($key, $value['value']);
                                break;
                            case 'between':
                                $builder->whereBetween($key, $value['value']);
                                break;
                            case 'not between':
                                $builder->whereNotBetween($key, $value['value']);
                                break;
                            default:
                                $builder->where($key, $operator, $value['value']);
                                break;
                        }
                    } else {
                        $builder->where($key, $value['value']);
                    }
                } else {
                    $builder->where($key, $value);
                }
            }
        }
    }
}