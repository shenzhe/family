<?php
//file frame/Family/Core/Singleton.php
namespace Family\Core;


trait Singleton
{
    private static $instance;

    static function getInstance(...$args)
    {
        if (!isset(self::$instance)) {
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}