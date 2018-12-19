<?php

namespace Family\Core;

use Family\Family;
use Family\Helper\Dir;

class Config
{

    /**
     * @var 配置map
     */
    public static $configMap = [];

    /**
     * @desc 读取配置，默认是application/config/default.php
     *          此配置不可热加载
     */
    public static function load()
    {
        $configPath = Family::$applicationPath .
            DS . 'config';
        self::$configMap = require $configPath .
            DS . 'default.php';
    }

    /**
     * @desc 读取配置，默认是application/config 下除default所有的php文件
     *          非default配置，可以热加载
     */
    public static function loadLazy()
    {
        $configPath = Family::$applicationPath .
            DS . 'config';
        $files = Dir::tree($configPath, "/.php$/");
        if (!empty($files)) {
            foreach ($files as $dir => $filelist) {
                foreach ($filelist as $file) {
                    if ('default.php' == $file) {
                        continue;
                    }
                    $filename = $dir . DS . $file;
                    self::$configMap += include "{$filename}";
                }
            }
        }
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
        if (isset(self::$configMap[$key])) {
            return self::$configMap[$key];
        }

        return $def;
    }
}