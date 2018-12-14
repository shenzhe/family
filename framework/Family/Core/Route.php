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