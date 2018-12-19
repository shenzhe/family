<?php

namespace Family\Core;


use Family\Pool\Context;
use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

class Route
{
    public static function dispatch()
    {
        $context = Context::getContext();
        $request = $context->getRequest();
        $path = $request->server['path_info'];
        if ('/favicon.ico' == $path) {
            return '';
        }
        $r = Config::get('router');
        //fastrouter
        if (!empty($r) && is_callable($r)) {
            $dispatcher = simpleDispatcher($r);
            $routeInfo = $dispatcher->dispatch($request->server['request_method'], $path);
            switch ($routeInfo[0]) {
                case Dispatcher::FOUND:
                    $class = new $routeInfo[1][0];
                    $method = $routeInfo[1][1];
                    return $class->$method();
                    break;
                case Dispatcher::NOT_FOUND:
                    return self::normal($path);
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    throw new \Exception("METHOD_NOT_ALLOWED");
                    break;
            }
        }
    }

    public static function normal($path)
    {
        //默认访问 controller/index.php 的 index方法
        if (empty($path) || '/' == $path) {
            $controller = 'Index';
            $method = 'Index';
        } else {
            $maps = explode('/', $path);

            if (count($maps) < 2) {
                $controller = 'Index';
                $method = 'Index';
            } else {
                $controller = $maps[1];
                if (empty($maps[2])) {
                    $method = 'Index';
                } else {
                    $method = $maps[2];
                }
            }
        }
        $controllerClass = "controller\\{$controller}";
        $class = new $controllerClass;

        return $class->$method();
    }
}