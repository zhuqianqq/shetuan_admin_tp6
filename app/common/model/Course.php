<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 课程
 * Class Teacher
 * @package app\model
 */
class Course extends BaseModel
{
    protected $pk = 'course_id';
    protected $table = "st_shetuan";

    CONST STATUS_UP = 1;//上架
    CONST STATUS_DOWN = 0;//上架

}

