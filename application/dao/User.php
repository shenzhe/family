<?php

namespace dao;


use Family\Db\Dao;
use Family\Core\Singleton;

class User extends Dao
{
    use Singleton;

    public function __construct()
    {
        parent::__construct('\entity\User');
    }
}