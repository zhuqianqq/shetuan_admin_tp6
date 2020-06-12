<?php

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\service\CourseService;
use app\admin\service\StTeacherService;
use app\admin\service\TeacherService;
use app\admin\validate\stTeacher as VT;
use think\exception\ValidateException;


class Teacher extends BaseController
{
    /**
     * 班主任老师列表
     */
    public function teacherList()
    {
        $param = [];
        $param['page_size']= input('pageSize',10, 'int');
        $param['courseId'] = input('courseId', '', 'int');
        $param['grade'] = input('grade', '', 'int');
        $param['teacherInfo'] = input('teacherInfo', '', 'string');
        $result = TeacherService::teacherList($param);

        return json_ok($result);
    }

    /**
     * 增加或修改老师
     * @Validate(VT::class,scene="save",batch="true")
     */
    public function teacherAddOrUpdate()
    {
        $data['teacher_name'] = input('post.teacherName', '', 'string');
        $data['mobile'] = input('post.mobile', '', 'string');
        $data['course_id'] = input('post.courseId', '', 'string');
        $data['grade'] = input('post.grade', '', 'string');
        $data['teacherId'] = input('post.teacherId', '', 'string');
        $data['class_id'] = input('post.classId', '', 'string');
        $data['grade'] = str_replace('，', ',', $data['grade']);
        $data['course_id'] = str_replace('，', ',', $data['course_id']);

        $result = TeacherService::addOrUpdate($data);
        return json_ok($result);
    }

    /**
     * 删除社团老师
     */
    public function techerDelete()
    {
        $teacherId = input('post.teacherId', '', 'int');
        if (!$teacherId) return json_error(10002);
        $result = TeacherService::teacherDelete($teacherId);

        return json_ok($result);
    }

}
