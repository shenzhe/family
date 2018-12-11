<?php
namespace Family\Core;


class Route
{
    public static function dispatch($path)
    {
        //默认访问 controller/index.php 的 index方法
        if (empty($path) || '/' == $path) {
            $controller = 'Index';
            $method = 'Index';
        } else {
            $maps = explode('/', $path);
            $controller = $maps[1];
            $method = $maps[2];
        }

        $controllerClass = "controller\\{$controller}";
        $class = new $controllerClass;

        return $class->$method();

    }
}