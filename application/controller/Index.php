<?php

//file: application/controller/Index.php
namespace controller;

use Family\Pool\Context;

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

}