<?php
//file: application/entity/user.phU

namespace entity;


use Family\MVC\Entity;

class User extends Entity
{
    /**
     * 对应的数据库表名
     */
    const TABLE_NAME = 'user';
    /**
     * 主键字段名
     */
    const PK_ID = 'id';

    //以下对应的数据库字段名
    public $id;
    public $name;
    public $password;

}