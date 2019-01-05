<?php
//file Family/Pool/Context.php
namespace Family\Pool;

use Family\Core\Singleton;
use Family\Coroutine\Coroutine;


/**
 * Class Context
 * @package Family\Coroutine
 * @desc 保持嵌套协程的context传递
 */
class Context implements PoolInterface
{

    use Singleton;
    /**
     * @var array context pool
     */
    private $pool = [];

    /**
     * @return \Family\Coroutine\Context
     * @desc 可以任意协程获取到context
     */
    public function get()
    {
        $id = Coroutine::getPid();
        if (isset($this->pool[$id])) {
            return $this->pool[$id];
        }

        return null;
    }

    /**
     * @desc 清除context
     */
    public function release()
    {
        $id = Coroutine::getPid();
        if (isset($this->pool[$id])) {
            unset($this->pool[$id]);
            Coroutine::clear($id);
        }
    }

    /**
     * @param $context
     * @desc 设置context
     */
    public function put($context)
    {
        $id = Coroutine::getPid();
        $this->pool[$id] = $context;
    }

    public function getLength()
    {
        return count($this->pool);
    }
}