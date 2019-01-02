<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2018-12-24
 * Time: 10:58
 */

namespace Family\MVC;


use Family\Core\Config;

class View
{
    public function render($data)
    {
        $mode = Config::get('view_mode', 'Json');

    }
}