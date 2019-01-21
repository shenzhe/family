<?php

namespace Family\Core;

use Family\Family;

class Config
{

    /**
     * @var 配置map
     */
    public static $config = null;

    /**
     * @desc 读取配置，默认是application/config/default.php
     *          此配置不可热加载
     */
    public static function load()
    {
        $configPath = Family::$applicationPath . DS . 'config';
        self::$config = \Noodlehaus\Config::load($configPath . DS . 'default.php');
    }

    /**
     * @desc 读取配置，默认是application/config 下除default所有的php文件
     *          非default配置，可以热加载
     */
    public static function loadLazy()
    {
        $configPath = Family::$applicationPath . DS . 'config/lazy';
        $config = new \Noodlehaus\Config($configPath);
        self::$config->merge($config);
    }

    /**
     * @param $key
     * @param $def
     * @desc 读取配置
     * @return string|null
     *
     */
    public static function get($key, $def = null)
    {
        return self::$config->get($key, $def);
    }
}