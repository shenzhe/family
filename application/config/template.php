<?php

use Family\Family;

return [
    'template' => [
        'path' => Family::$applicationPath . DS . 'template' . DS . 'default',    //模版目录, 空则默认 template/default
        'cache' => Family::$applicationPath . DS . 'template' . DS . 'default_cache',    //缓存目录, 空则默认 template/default_cache
    ]
];