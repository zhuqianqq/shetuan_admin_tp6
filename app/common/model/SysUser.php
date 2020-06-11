<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 用户
 * Class SysUser
 * @package app\model
 */
class SysUser extends BaseModel
{
    public static $_table = "sys_user";
}

