<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2018-12-17
 * Time: 20:44
 */

namespace service;

use dao\User as UserDao;
use Family\Core\Singleton;


class User
{
    use Singleton;

    public function getUserInfoByUId($id)
    {
        return UserDao::getInstance()->fetchById($id);
    }

    public function getUserInfoList()
    {
        return UserDao::getInstance()->fetchAll();
    }

    public function add(array $array)
    {
        return UserDao::getInstance()->add($array);
    }

    public function updateById(array $array, $id)
    {
        return UserDao::getInstance()->update($array, "id={$id}");
    }

    public function deleteById($id)
    {
        return UserDao::getInstance()->delete("id={$id}");
    }
}