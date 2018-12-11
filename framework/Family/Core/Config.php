<?php

namespace Family\Core;

class Config
{

    /**
     * @var 配置map
     */
    public static $configMap;

    /**
     * @desc 读取配置，默认是application/config/default.php
     */
    public static function load()
    {
        $configPath = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config';
        self::$configMap = require $configPath . DIRECTORY_SEPARATOR . 'default.php';
    }

    /**
     * @param $key
     * @desc 读取配置
     * @return string|null
     *
     */
    public static function get($key)
    {
        if(isset(self::$configMap[$key])) {
            return self::$configMap[$key];
        }

        return null;
    }
}