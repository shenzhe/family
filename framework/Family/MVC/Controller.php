<?php
//file framework/Family/MVC/Controller.php
namespace Family\MVC;


use Family\Core\Config;
use Family\Helper\Template;
use Family\Pool\Context;

class Controller
{

    /**
     * @var \EasySwoole\Http\Request
     */
    protected $request;
    /**
     * @var \Twig\Environment
     */
    protected $template;

    const _CONTROLLER_KEY_ = '__CTR__';
    const _METHOD_KEY_ = '__METHOD__';

    public function __construct()
    {
        //通过context拿到$request, 再也不用担收数据错乱了
        /**
         * @var $context \Family\Coroutine\Context
         */
        $context = Context::getInstance()->get();
        $this->request = $context->getRequest();
        $this->template = Template::getInstance()->template;
    }

}