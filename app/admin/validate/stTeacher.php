<?php
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

/**
 * 社团老师
 * Class stTeacher
 * @package app\validate
 * @date 2019-11-27
 */
class stTeacher extends Validate
{
    //验证规则
    protected $rule = [
        'teacherName' => ['require', 'max' => '24'],
        'mobile' => ['require','mobile'],
        'courseId' => ['require'],
        'grade'   => ['require']
    ];

    //提示信息
    protected $message = [
        'teacherName.require'   => '社团老师名字必须',
        'teacherName.max'       => '社团老师名字最多不能超过24个字符',
        'mobile.require' => '手机号必须',
        'mobile.mobile' => '手机号格式错误',
        'courseId'    => '课程必须选择',
        'grade'   => '请选择年级',
    ];

    //验证场景
    protected $scene = [
        'save' => ['teacherName', 'mobile', 'courseId', 'grade'],
    ];


}
