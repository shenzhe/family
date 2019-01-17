<?php
//file framework/Family/Db/Mysql.php
namespace Family\Db;

use Family\Core\Log;
use Swoole\Coroutine\MySQL as SwMySql;

class Mysql
{
    /**
     * @var MySQL
     */
    private $master;   //主数据库连接
    private $slave;     //从数据库连接list
    private $config;    //数据库配置

    /**
     * @param $config
     * @return mixed
     * @throws \Exception
     * @desc 连接mysql
     */
    public function connect($config)
    {
        //创建主数据连接
        $master = new SwMySql();
        $res = $master->connect($config['master']);
        if ($res === false) {
            //连接失败，抛弃常
            throw new \Exception($master->connect_error, $master->errno);
        } else {
            //存入master资源
            $this->master = $master;
        }

        if (!empty($config['slave'])) {
            //创建从数据库连接
            foreach ($config['slave'] as $conf) {
                $slave = new SwMySql();
                $res = $slave->connect($conf);
                if ($res === false) {
                    //连接失败，抛弃常
                    throw new \Exception($slave->connect_error, $slave->errno);
                } else {
                    //存入slave资源
                    $this->slave[] = $slave;
                }
            }
        }

        $this->config = $config;
        return $res;
    }

    /**
     * @param $type
     * @param $index
     * @return MySQL
     * @desc 单个数据库重连
     * @throws \Exception
     */
    public function reconnect($type, $index)
    {
        //通过type判断是主还是从
        if ('master' == $type) {
            //创建主数据连接
            $master = new SwMySql();
            $res = $master->connect($this->config['master']);
            if ($res === false) {
                //连接失败，抛弃常
                throw new \Exception($master->connect_error, $master->errno);
            } else {
                //更新主库连接
                $this->master = $master;
            }
            return $this->master;
        }

        if (!empty($this->config['slave'])) {
            //创建从数据连接
            $slave = new SwMySql();
            $res = $slave->connect($this->config['slave'][$index]);
            if ($res === false) {
                //连接失败，抛弃常
                throw new \Exception($slave->connect_error, $slave->errno);
            } else {
                //更新对应的重库连接
                $this->slave[$index] = $slave;
            }
            return $slave;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @desc 利用__call,实现操作mysql,并能做断线重连等相关检测
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $sql = $arguments[0];
        $res = $this->chooseDb($sql);
        $db = $res['db'];
//        $result = call_user_func_array([$db, $name], $arguments);
        $result = $db->$name($sql);
        Log::info($sql);
        if (false === $result) {
            Log::warning('mysql query:{sql} false', ['{sql}' => $sql]);
            if (!$db->connected) { //断线重连
                $db = $this->reconnect($res['type'], $res['index']);
                Log::info('mysql reconnect', $res);
                $result = $db->$name($sql);
                return $this->parseResult($result, $db);
            }

            if (!empty($db->errno)) {  //有错误码，则抛出弃常
                throw new \Exception($db->error, $db->errno);
            }
        }
        return $this->parseResult($result, $db);
    }

    /**
     * @param $result
     * @param $db MySQL
     * @return array
     * @desc 格式化返回结果：查询：返回结果集，插入：返回新增id, 更新删除等操作：返回影响行数
     */
    public function parseResult($result, $db)
    {
        if ($result === true) {
            return [
                'affected_rows' => $db->affected_rows,
                'insert_id' => $db->insert_id,
            ];
        }
        return $result;
    }


    /**
     * @param $sql
     * @desc 根据sql语句，选择主还是从
     * @ 判断有select 则选择从库， insert, update, delete等选择主库
     * @return array
     */
    protected function chooseDb($sql)
    {
        if (!empty($this->slave)) {
            //查询语句，随机选择一个从库
            if ('select' == strtolower(substr($sql, 0, 6))) {
                if (1 == count($this->slave)) {
                    $index = 0;
                } else {
                    $index = array_rand($this->slave);
                }
                return [
                    'type' => 'slave',
                    'index' => $index,
                    'db' => $this->slave[$index],

                ];
            }
        }

        return [
            'type' => 'master',
            'index' => 0,
            'db' => $this->master
        ];
    }

    /**
     * @desc 回收资源
     */
    public function release()
    {
        $this->master->close();
        if (!empty($this->slave)) {
            foreach ($this->slave as $slave) {
                $slave->close();
            }
        }
    }

    /**
     * @return mixed
     * @desc 返回配置信息
     */
    public function getConfig()
    {
        return $this->config;
    }

}
