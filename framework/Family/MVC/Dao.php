<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2018-12-17
 * Time: 19:44
 */

namespace Family\MVC;

use Family\Db\Mysql;
use Family\Pool\Mysql as MysqlPool;
use Family\Coroutine\Coroutine;


class Dao
{
    private $entity;

    private $dbs;

    /**
     * @var Mysql
     */
    private $db;

    //表名
    private $table;

    //主键字段名
    private $pkId;


    public function __construct($entity)
    {
        $this->entity = $entity;
        $coId = Coroutine::getId();
        if (empty($this->dbs[$coId])) {
            //不同协程不对复用mysql连接，所以通过协程id进行资源隔离
            $this->dbs[$coId] = MysqlPool::getInstance()->get();
            $entityRef = new \ReflectionClass($this->entity);
            $this->table = $entityRef->getConstant('TABLE_NAME');
            $this->pkId = $entityRef->getConstant('PK_ID');
            defer(function () {
                //回收资源
                $this->recycle();
            });
        }
        $this->db = $this->dbs[$coId];
    }

    public function recycle()
    {
        $coId = Coroutine::getId();
        if (!empty($this->dbs[$coId])) {
            $mysql = $this->dbs[$coId];
            MysqlPool::getInstance()->put($mysql);
            unset($this->dbs[$coId]);
        }
    }

    public function getLibName()
    {
        return $this->table;
    }

    /**
     * @param $id
     * @param string $fields
     * @return mixed
     * @desc 通过主键查询记录
     */
    public function fetchById($id, $fields = '*')
    {
        return $this->fetchEntity("{$this->pkId} = {$id}", $fields);
    }

    /**
     * @param string $where
     * @param string $fields
     * @param null $orderBy
     * @return mixed
     * @desc 通过条件查询一条记录，并返回一个entity
     */
    public function fetchEntity($where = '1', $fields = '*', $orderBy = null)
    {
        $result = $this->fetchArray($where, $fields, $orderBy, 1);
        if (!empty($result[0])) {
            return new $this->entity($result[0]);
        }
        return null;
    }

    /**
     * @param string $where
     * @param string $fields
     * @param null $orderBy
     * @param int $limit
     * @return mixed
     * @desc 通过条件查询记录列表，并返回entity列表
     */
    public function fetchAll($where = '1', $fields = '*', $orderBy = null, $limit = 0)
    {
        $result = $this->fetchArray($where, $fields, $orderBy, $limit);
        if (empty($result)) {
            return $result;
        }
        foreach ($result as $index => $value) {
            $result[$index] = new $this->entity($value);
        }
        return $result;
    }


    /**
     * @param string $where
     * @param string $fields
     * @param null $orderBy
     * @param int $limit
     * @return mixed
     * @desc 通过条件查询
     */
    public function fetchArray($where = '1', $fields = '*', $orderBy = null, $limit = 0)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        if ($limit) {
            $query .= " limit {$limit}";
        }
        return $this->db->query($query);
    }

    /**
     * @param array $array
     * @return bool
     * @desc 插入一条记录
     */
    public function add(array $array)
    {

        $strFields = '`' . implode('`,`', array_keys($array)) . '`';
        $strValues = "'" . implode("','", array_values($array)) . "'";
        $query = "INSERT INTO {$this->getLibName()} ({$strFields}) VALUES ({$strValues})";
        if (!empty($onDuplicate)) {
            $query .= 'ON DUPLICATE KEY UPDATE ' . $onDuplicate;
        }
        echo $query . PHP_EOL;
        $result = $this->db->query($query);
        if (!empty($result['insert_id'])) {
            return $result['insert_id'];
        }

        return false;
    }

    /**
     * @param array $array
     * @param $where
     * @return bool
     * @throws \Exception
     * @desc 按条件更新记录
     */
    public function update(array $array, $where)
    {
        if (empty($where)) {
            throw new \Exception('update 必需有where条件限定');
        }
        $strUpdateFields = '';
        foreach ($array as $key => $value) {
            $strUpdateFields .= "`{$key}` = '{$value}',";
        }
        $strUpdateFields = rtrim($strUpdateFields, ',');
        $query = "UPDATE {$this->getLibName()} SET {$strUpdateFields} WHERE {$where}";
        echo $query;
        $result = $this->db->query($query);
        return $result['affected_rows'];
    }

    /**
     * @param $where
     * @return mixed
     * @throws \Exception
     * @desc 按条件删除记录
     */
    public function delete($where)
    {
        if (empty($where)) {
            throw new \Exception('delete 必需有where条件限定');
        }

        $query = "DELETE FROM {$this->getLibName()} WHERE {$where}";
        $result = $this->db->query($query);
        return $result['affected_rows'];
    }
}