<?php

use Family\Family;

return [
    'template' => [
        //模板页面的存放目录
        'path' => Family::$applicationPath . DS . 'template' . DS . 'default',    //模版目录, 空则默认 template/default
        //模板缓存页面的存放目录
        'cache' => Family::$applicationPath . DS . 'template' . DS . 'default_cache',    //缓存目录, 空则默认 template/default_cache
    ]
];