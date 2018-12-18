<?php
//file: framework/family/Core/Log.php
namespace Family\Core;


use Family\Family;

use SeasLog;

class Log
{
    private static $seaslog = false;

    //设置日志目录
    public static function init()
    {
        if (class_exists('SeasLog')) {
            self::$seaslog = true;
            SeasLog::setBasePath(Family::$applicationPath . DS . 'log');
        }
    }

    //代理seaglog的静态方法，如 SeasLog::debug
    public static function __callStatic($name, $arguments)
    {
        if (self::$seaslog) {
            forward_static_call_array(['SeasLog', $name], $arguments);
        }
    }

}