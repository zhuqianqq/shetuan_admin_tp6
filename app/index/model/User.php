<?php
declare (strict_types=1);

namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 用户
 * Class User
 * @package app\model
 */
class User extends BaseModel
{
    public static $_table = "st_user"; 
}

