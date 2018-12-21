<?php

namespace Family\Core;


use Family\MVC\Controller;
use Family\Pool\Context;
use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

class Route
{
    /**
     * @throws \Exception
     * @desc 自动路由
     */
    public static function dispatch()
    {
        $context = Context::getContext();
        $request = $context->getRequest();
        $response = $context->getResponse();
        $path = $request->getUri()->getPath();
        if ('/favicon.ico' == $path) {
            return '';
        }
        $r = Config::get('router');

        if (empty($r) || !is_callable($r)) {
            return self::normal($path, $request);

        }

        //fastrouter
        $dispatcher = simpleDispatcher($r);
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $path);
        if (Dispatcher::FOUND === $routeInfo[0]) {
            //匹配数组, 格式：['controllerName', 'MethodName']
            if (is_array($routeInfo[1])) {
                if (!empty($routeInfo[2]) && is_array($routeInfo[2])) {
                    //有默认参数
                    $params = $request->getQueryParams() + $routeInfo[2];
                    $request->withQueryParams($params);
                }
                $request->withAttribute(Controller::_CONTROLLER_KEY_, $routeInfo[1][0]);
                $request->withAttribute(Controller::_METHOD_KEY_, $routeInfo[1][1]);
                $controller = new $routeInfo[1][0]();
                $methodName = $routeInfo[1][1];
                $result = $controller->$methodName();
            } elseif (is_string($routeInfo[1])) {
                //字符串, 格式：controllerName@MethodName
                list($controllerName, $methodName) = explode('@', $routeInfo[1]);
                if (!empty($routeInfo[2]) && is_array($routeInfo[2])) {
                    //有默认参数
                    $params = $request->getQueryParams() + $routeInfo[2];
                    $request->withQueryParams($params);
                }
                $request->withAttribute(Controller::_CONTROLLER_KEY_, $controllerName);
                $request->withAttribute(Controller::_METHOD_KEY_, $methodName);
                $controller = new $controllerName();
                $result = $controller->$methodName();
            } elseif (is_callable($routeInfo[1])) {
                //回调函数，直接执行
                $result = $routeInfo[1](...$routeInfo[2]);
            } else {
                throw new \Exception('router error');
            }

            return $result;
        }

        if (Dispatcher::NOT_FOUND === $routeInfo[0]) {
            //没找到路由，走默认的路由 http://xxx.com/{controllerName}/{MethodName}
            return self::normal($path, $request);

        }


        if (Dispatcher::METHOD_NOT_ALLOWED === $routeInfo[0]) {
            throw new \Exception("METHOD_NOT_ALLOWED");
        }
    }

    /**
     * @param $path
     * @param $request
     * @return mixed
     * @desc 没有匹配到路由，走默认的路由规则 http://xxx.com/{controllerName}/{MethodName}
     */
    public static function normal($path, $request)
    {
        //默认访问 controller/index.php 的 index方法
        if (empty($path) || '/' == $path) {
            $controllerName = 'Index';
            $methodName = 'Index';
        } else {
            $maps = explode('/', $path);

            if (count($maps) < 2) {
                $controllerName = 'Index';
                $methodName = 'Index';
            } else {
                $controllerName = $maps[1];
                if (empty($maps[2])) {
                    $methodName = 'Index';
                } else {
                    $methodName = $maps[2];
                }
            }
        }
        $controllerName = "controller\\{$controllerName}";
        $request->withAttribute(Controller::_CONTROLLER_KEY_, $controllerName);
        $request->withAttribute(Controller::_METHOD_KEY_, $methodName);
        $controller = new $controllerName();
        return $controller->$methodName();
    }
}