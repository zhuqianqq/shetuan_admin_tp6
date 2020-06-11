<?php
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

/**
 * 管理员
 * Class Admin
 * @package app\validate
 * @date 2019-11-27
 */
class Course extends Validate
{
    //验证规则
    protected $rule = [
        'course_name' => ['require', 'max' => '24'],
        'status' => ['require'],
        'weeks' => ['require'],
        'start_time'    => ['require'],
        'end_time'    => ['require'],
        'class_place'   => ['require'],
        'grade'   => ['require']
    ];

    //提示信息
    protected $message = [
        'course_name.require'   => '课程名称必须',
        'course_name.max'       => '课程名称最多不能超过24个字符',
        'status' => '课程状态必须',
        'weeks'    => '课程安排必须选择',
        'start_time'           => '请选择课程开始时间',
        'end_time'           => '请选择课程结束时间',
        'class_place'        => '请选择课程地点',
        'grade'              => '请选择年级',
    ];

    //验证场景
    protected $scene = [
        'save' => ['username', 'group_id', 'phone', 'email'],
    ];


}
