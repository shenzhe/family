<?php
//file framework/Family/MVC/Controller.php
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