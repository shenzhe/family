<?php
/**
 * Created by PhpStorm.
 * User: shenzhe
 * Date: 2018-12-18
 * Time: 17:08
 */

namespace Family\MVC;


class Entity
{
    public function __construct(array $array)
    {
        if (empty($array)) {
            return $this;
        }

        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}