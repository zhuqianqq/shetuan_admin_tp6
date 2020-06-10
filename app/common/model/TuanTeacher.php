<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 社团老师
 * Class TuanTeacher
 * @package app\model
 */
class TuanTeacher extends BaseModel
{
    public static $_table = "st_tuan_teacher"; 
}

