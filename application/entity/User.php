<?php

namespace entity;


use Family\MVC\Entity;

class User extends Entity
{
    const TABLE_NAME = 'user';
    const PK_ID = 'id';
    public $id;
    public $name;
    public $password;

}