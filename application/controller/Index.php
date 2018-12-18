<?php

//file: application/controller/Index.php
namespace controller;


use Family\MVC\Controller;
use service\User as UserService;

class Index extends Controller
{
    public function index()
    {

        return 'i am family by route!' . json_encode($this->request->get);
    }

    public function tong()
    {
        return 'i am tong ge';
    }

    public function user()
    {
        if (empty($this->request->get['uid'])) {
            throw new \Exception("uid 不能为空 ");
        }
        $result = UserService::getInstance()->getUserInfoByUId($this->request->get['uid']);
        return json_encode($result);

    }

    public function list()
    {
        $result = UserService::getInstance()->getUserInfoList();
        return json_encode($result);

    }

    public function add()
    {
        $array = [
            'name' => $this->request->get['name'],
            'password' => $this->request->get['password'],
        ];

        return UserService::getInstance()->add($array);
    }

    public function update()
    {
        $array = [
            'name' => $this->request->get['name'],
            'password' => $this->request->get['password'],
        ];
        $id = $this->request->get['id'];
        return UserService::getInstance()->updateById($array, $id);
    }

    public function delete()
    {
        $id = $this->request->get['id'];
        return UserService::getInstance()->deleteById($id);
    }

}