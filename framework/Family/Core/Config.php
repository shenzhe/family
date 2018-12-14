<?php

namespace Family\Core;

use Family\Family;

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
        $configPath = Family::$applicationPath .
                        DS . 'config';
        self::$configMap = require $configPath .
                        DS . 'default.php';
    }

    /**
     * @param $key
     * @desc 读取配置
     * @return string|null
     *
     */
    public static function get($key)
    {
        if (isset(self::$configMap[$key])) {
            return self::$configMap[$key];
        }

        return null;
    }
}