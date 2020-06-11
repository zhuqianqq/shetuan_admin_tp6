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
        'courseName' => ['require', 'max' => '24'],
        'status' => ['require'],
        'weeks' => ['require'],
        'startTime'    => ['require'],
        'endTime'    => ['require'],
        'classPlace'   => ['require'],
        'grade'   => ['require']
    ];

    //提示信息
    protected $message = [
        'courseName.require'   => '课程名称必须',
        'courseName.max'       => '课程名称最多不能超过24个字符',
        'status' => '课程状态必须',
        'weeks'    => '课程安排必须选择',
        'startTime'           => '请选择课程开始时间',
        'endTime'           => '请选择课程结束时间',
        'classPlace'        => '请选择课程地点',
        'grade'              => '请选择年级',
    ];

    //验证场景
    protected $scene = [
        'save' => ['courseName', 'status', 'weeks', 'startTime', 'endTime', 'classPlace', 'grade'],
    ];


}
