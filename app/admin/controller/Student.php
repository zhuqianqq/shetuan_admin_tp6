<?php

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\service\CourseService;
use app\admin\service\StTeacherService;
use app\admin\service\StudentService;
use app\admin\service\TeacherService;
use app\admin\validate\stTeacher as VT;
use think\exception\ValidateException;


class Student extends BaseController
{
    /**
     * 班主任老师列表
     */
    public function studentList()
    {
        $param = [];
        $param['page_size']= input('pageSize',10, 'int');
        $param['classId'] = input('classId', '', 'int');
        $param['grade'] = input('grade', '', 'int');
        $param['studentInfo'] = input('teacherInfo', '', 'string');
        $result = StudentService::studentList($param);

        return json_ok($result);
    }

    /**
     * 增加或修改老师
     */
    public function studentAddOrUpdate()
    {
        $data['student_num'] = input('post.studentNum', '', 'string');
        $data['student_name'] = input('post.studentName', '', 'string');
        $data['mobile'] = input('post.mobile', '', 'string');
        $data['class_id'] = input('post.classId', '', 'string');
        $data['grade'] = input('post.grade', '', 'string');
        $data['parent_name'] = input('post.parentName', '', 'string');
        $data['address'] = input('post.address', '', 'string');
        $data['student_id'] = input('post.studentId', '', 'string');

        $result = StudentService::addOrUpdate($data);
        return json_ok($result);
    }

    /**
     * 删除社团老师
     */
    public function studentDelete()
    {
        $studentId = input('post.studentId', '', 'int');
        if (!$studentId) return json_error(10002);
        $result = StudentService::studentDelete($studentId);

        return json_ok($result);
    }

}
