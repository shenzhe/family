<?php
//file framework/Family/Pool/Mysql.php
namespace Family\Pool;

use Family\Db\Mysql as DB;
use chan;

class Mysql implements PoolInterface
{
    private static $instances;
    private $pool;  //连接池容器，一个channel
    private $config;

    /**
     * @param null $config
     * @return Mysql
     * @desc 获取连接池实例
     * @throws \Exception
     */
    public static function getInstance($config = null)
    {

        if (empty($config)) {
            if (!empty(self::$instances)) {
                //如果没有配置config, 默认返回第一个连接池
                return current(self::$instances);
            }
            throw new \Exception("mysql config empty");
        }

        if (empty($config['name'])) {
            $config['name'] = $config['master']['host'] . ':' . $config['master']['port'];
        }

        if (empty(self::$instances[$config['name']])) {
            self::$instances[$config['name']] = new static($config);
        }

        return self::$instances[$config['name']];
    }

    /**
     * Mysql constructor.
     * @param $config
     * @throws \Exception
     * @desc 初始化，自动创建实例,需要放在workerstart中执行
     */
    public function __construct($config)
    {
        if (empty($this->pool)) {
            $this->config = $config;
            $this->pool = new chan($config['pool_size']);
            for ($i = 0; $i < $config['pool_size']; $i++) {
                $mysql = new DB();
                $res = $mysql->connect($config);
                if ($res == false) {
                    //连接失败，抛弃常
                    throw new \Exception("failed to connect mysql server.");
                } else {
                    //mysql连接存入channel
                    $this->put($mysql);
                }
            }
        }
    }

    /**
     * @param $mysql
     * @throws \Exception
     * @desc 放入一个mysql连接入池
     */
    public function put($mysql)
    {
        if ($this->getLength() >= $this->config['pool_size']) {
            throw new \Exception("pool full");
        }
        $this->pool->push($mysql);
    }

    /**
     * @return mixed
     * @desc 获取一个连接，当超时，返回一个异常
     * @throws \Exception
     */
    public function get()
    {
        $mysql = $this->pool->pop($this->config['pool_get_timeout']);
        if (false === $mysql) {
            throw new \Exception("get mysql timeout, all mysql connection is used");
        }
        return $mysql;
    }

    /**
     * @return mixed
     * @desc 获取当时连接池可用对象
     */
    public function getLength()
    {
        return $this->pool->length();
    }

    /**
     * @return bool|mixed
     * @desc 回收处理
     */
    public function release()
    {
        if ($this->getLength() < $this->config['pool_size']) {
            //还有未归源的资源
            return true;
        }
        for ($i = 0; $i < $this->config['pool_size']; $i++) {
            $db = $this->pool->pop($this->config['pool_get_timeout']);
            if (false !== $db) {
                $db->release();
            }
        }
        return $this->pool->close();
    }
}
