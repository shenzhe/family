<?php
//file framework/Family/Pool/Any.php
namespace Family\Pool;

use chan;
use Family\Core\Singleton;

class Any implements PoolInterface
{
    private $pool;  //连接池容器，一个channel
    private $tag = 'default';
    private $length = 0;
    private $timeout = 0;


    use Singleton;


    public function __construct($tag = null, int $length = 0, int $timeout = 0)
    {
        if (empty($tag) || $length < 1) {
            throw new \Exception("tag不能为空, length不能小于1");
        }
        $this->tag = $tag;
        $this->length = $length;
        $this->timeout = $timeout;
        $this->pool = new chan($this->length);
    }

    /**
     * @param $resource
     * @throws \Exception
     * @desc 放入一个mysql连接入池
     */
    public function put($resource)
    {
        if ($this->getLength() >= $this->length) {
            throw new \Exception("pool full");
        }
        $this->pool->push($resource);
    }

    /**
     * @return mixed
     * @desc 获取一个连接，当超时，返回一个异常
     * @throws \Exception
     */
    public function get()
    {
        $mysql = $this->pool->pop($this->timeout);
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

    public function release()
    {
        if ($this->getLength() < $this->config['pool_size']) {
            //还有未归源的资源
            return true;
        }
        for ($i = 0; $i < $this->length; $i++) {
            $resource = $this->pool->pop($this->timeout);
            if (false !== $resource) {
                if (is_object($resource)
                    && method_exists($resource, 'release')) {
                    $resource->release();
                }
                unset($resource);
            }
        }
        return $this->pool->close();
    }
}
