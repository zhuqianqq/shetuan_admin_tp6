<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 通知消息
 * Class Message
 * @package app\model
 */
class Message extends BaseModel
{
    protected $table = "st_message"; 
}

