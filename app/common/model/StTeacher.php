<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;

/**
 * 社团老师
 * Class StTeacher
 * @package app\model
 */
class StTeacher extends BaseModel
{
    protected $pk = 'teacher_id';
    protected $table = "st_tuan_teacher";
}

