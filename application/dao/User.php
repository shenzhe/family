<?php
//file application/dao/User.php
namespace dao;


use Family\MVC\Dao;
use Family\Core\Singleton;

class User extends Dao
{
    use Singleton;

    public function __construct()
    {
        parent::__construct('\entity\User');
    }
}