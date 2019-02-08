<?php

//file: application/controller/Index.php
namespace controller;


use service\User as UserService;

class Index extends Base
{
    public function index()
    {
        return $this->template(['name' => 'tong']);
    }

    public function tong()
    {
        return $this->json('i am tong ge');
    }

    /**
     * @return false|string
     * @throws \Exception
     * @desc 返回一个用户信息
     */
    public function user()
    {
        $uid = $this->request->getQueryParam('uid');
        if (empty($uid)) {
            throw new \Exception("uid 不能为空 ");
        }
        $result = UserService::getInstance()->getUserInfoByUId($uid);
        return $this->json($result);
    }

    /**
     * @return false|string
     * @desc 返回用户列表
     */
    public function list()
    {
        $result = UserService::getInstance()->getUserInfoList();
        return $this->json($result);

    }

    /**
     * @return bool
     * @desc 添加用户
     */
    public function add()
    {
        $array = [
            'name' => $this->request->getQueryParam('name'),
            'password' => $this->request->getQueryParam('password'),
        ];

        return $this->json(UserService::getInstance()->add($array));
    }

    /**
     * @return bool
     * @throws \Exception
     * @desc 更新用户信息
     */
    public function update()
    {
        $array = [
            'name' => $this->request->getQueryParam('name'),
            'password' => $this->request->getQueryParam('password'),
        ];
        $id = $this->request->getQueryParam('id');
        return $this->json(UserService::getInstance()->updateById($array, $id));
    }

    /**
     * @return mixed
     * @throws \Exception
     * @desc 删除用户信息
     */
    public function delete()
    {
        $id = $this->request->getQueryParam('id');
        return $this->json(UserService::getInstance()->deleteById($id));
    }

}