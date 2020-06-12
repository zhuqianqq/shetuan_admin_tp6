<?php
declare (strict_types=1);

namespace app\admin\validate;

use think\Validate;

/**
 * 社团老师
 * Class Teacher
 * @package app\validate
 * @date 2019-11-27
 */
class Student extends Validate
{
    //验证规则
    protected $rule = [
        'studentNum' => ['require', 'max' => '10'],
        'studentName' => ['require', 'max' => '8'],
        'mobile' => ['require','mobile'],
        'grade'   => ['require'],
        'classId'   => ['require'],
    ];

    //提示信息
    protected $message = [
        'studentNum.require'   => '学生编号必须',
        'studentNum.max'       => '学生编号不能超过10个字符',
        'studentName.require'   => '学生名字必须',
        'studentName.max'       => '学生名字最多不能超过8个字符',
        'mobile.require' => '手机号必须',
        'mobile.mobile' => '手机号格式错误',
        'classId'    => '班级必须选择',
        'grade'   => '请选择年级',
    ];

    //验证场景
    protected $scene = [
        'save' => ['studentNum', 'mobile', 'studentName', 'classId','grade'],
    ];


}
