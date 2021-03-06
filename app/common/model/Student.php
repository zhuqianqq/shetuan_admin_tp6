<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 学生model
 * Class Student
 * @package app\model
 */
class Student extends BaseModel
{
    protected $pk = 'student_id';
    protected $table = "st_student";
    public static $_table = "st_student"; 
}

