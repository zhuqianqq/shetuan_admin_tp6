<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 学生签到表
 * Class RollCall
 * @package app\model
 */
class RollCall extends BaseModel
{
    public static $_table = "st_roll_call";  
}

