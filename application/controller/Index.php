<?php

//file: application/controller/Index.php
namespace controller;

use Family\Pool\Context;
use service\User as UserService;

class Index
{
    public function index()
    {
        //通过context拿到$request, 再也不用担收数据错乱了
        $context = Context::getContext();
        $request = $context->getRequest();
        return 'i am family by route!' . json_encode($request->get);
    }

    public function tong()
    {
        return 'i am tong ge';
    }

    public function user()
    {
        //通过context拿到$request, 再也不用担收数据错乱了
        $context = Context::getContext();
        $request = $context->getRequest();
        if (empty($request->get['uid'])) {
            throw new \Exception("uid 不能为空 ");
        }
        $result = UserService::getInstance()->getUserInfoByUId($request->get['uid']);
        return json_encode($result);

    }

    public function list()
    {
        $result = UserService::getInstance()->getUserInfoList();
        return json_encode($result);

    }

}