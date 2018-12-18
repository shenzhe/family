<?php

namespace entity;


class User
{
    const TABLE_NAME = 'user';
    const PK_ID = 'id';
    public $id;
    public $name;
    public $password;

    public function __construct(array $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}