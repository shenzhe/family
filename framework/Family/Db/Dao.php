<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2018-12-17
 * Time: 19:44
 */

namespace Family\Db;

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

    public function fetchById($id, $fields = '*')
    {
        return $this->fetchEntity("{$this->pkId} = {$id}", $fields);
    }

    public function fetchEntity($where = '1', $fields = '*', $orderBy = null)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        $query .= " limit 1";
        $result = $this->db->query($query);
        if (empty($result)) {
            return $result;
        }
        return new $this->entity($result[0]);
    }

    public function fetchAll($where = '1', $fields = '*', $orderBy = null, $limit = 0)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        if ($limit) {
            $query .= " limit {$limit}";
        }
        $result = $this->db->query($query);
        if (empty($result)) {
            return $result;
        }
        foreach ($result as $index => $value) {
            $result[$index] = new $this->entity($value);
        }
        return $result;
    }
}