<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2018-12-18
 * Time: 17:08
 */

namespace Family\MVC;


use Family\Pool\Context;

class Controller
{
    protected $request;

    public function __construct()
    {
        //通过context拿到$request, 再也不用担收数据错乱了
        $context = Context::getContext();
        $this->request = $context->getRequest();
    }
}