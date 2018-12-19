<?php
//file application/service/User.php
namespace service;

use dao\User as UserDao;
use Family\Core\Singleton;


class User
{
    use Singleton;

    /**
     * @param $id
     * @return mixed
     * @desc 通过uid查询用户信息
     */
    public function getUserInfoByUId($id)
    {
        return UserDao::getInstance()->fetchById($id);
    }

    /**
     * @return mixed
     * @desc 获取所有用户列表
     */
    public function getUserInfoList()
    {
        return UserDao::getInstance()->fetchAll();
    }

    /**
     * @param array $array
     * @return bool
     * @desc 添加一个用户
     */
    public function add(array $array)
    {
        return UserDao::getInstance()->add($array);
    }

    /**
     * @param array $array
     * @param $id
     * @return bool
     * @throws \Exception
     * @desc 按id更新一个用户
     */
    public function updateById(array $array, $id)
    {
        return UserDao::getInstance()->update($array, "id={$id}");
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     * @desc 按id删除用户
     */
    public function deleteById($id)
    {
        return UserDao::getInstance()->delete("id={$id}");
    }
}