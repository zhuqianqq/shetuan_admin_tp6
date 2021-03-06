<?php

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\service\CourseService;
use app\admin\service\StTeacherService;
use app\admin\validate\stTeacher as VT;
use think\exception\ValidateException;


class StTeacher extends BaseController
{
    /**
     * 社团老师列表
     */
    public function stTeacherList()
    {
        $param = [];
        $param['page_size']= input('pageSize',10, 'int');
        $param['courseId'] = input('courseId', '', 'int');
        $param['grade'] = input('grade', '', 'int');
        $param['teacherInfo'] = input('teacherInfo', '', 'string');
        $result = StTeacherService::stTeacherList($param);

        return json_ok($result);
    }

    /**
     * 增加或修改社团老师
     * @Validate(VT::class,scene="save",batch="true")
     */
    public function stTeacherAddOrUpdate()
    {
        $data['teacher_name'] = input('post.teacherName', '', 'string');
        $data['mobile'] = input('post.mobile', '', 'string');
        $data['course_id'] = input('post.courseId', '', 'string');
        $data['grade'] = input('post.grade', '', 'string');
        $data['teacherId'] = input('post.teacherId', '', 'string');
        $data['grade'] = str_replace('，', ',', $data['grade']);
        $data['course_id'] = str_replace('，', ',', $data['course_id']);

        $result = StTeacherService::addOrUpdate($data);
        return json_ok($result);
    }

    /**
     * 删除社团老师
     */
    public function stTecherDelete()
    {
        $teacherId = input('post.teacherId', '', 'int');
        if (!$teacherId) return json_error(10002);
        $result = StTeacherService::stTeacherDelete($teacherId);

        return json_ok($result);
    }

    /**
     * 课程下架
     */
    public function courseOnOrOff()
    {
        $courseId = input('post.courseId', '', 'int');
        $status = input('post.status', '', 'int');
        if (!$courseId || !$status) return json_error(10002);
        $result = CourseService::courseOnOrOff($courseId, $status);

        return json_ok($result);
    }
}
