<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 老师
 * Class Teacher
 * @package app\model
 */
class Teacher extends BaseModel
{
    protected $pk = 'teacher_id';
    public static $_table = "st_teacher"; 
}

