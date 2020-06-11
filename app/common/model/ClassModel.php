<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 班级model
 * Class Class
 * @package app\model
 */
class ClassModel extends BaseModel
{
 
    protected $table = "st_class";

}

