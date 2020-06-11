<?php

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\service\CourseService;
use app\admin\validate\Course as VC;
use think\exception\ValidateException;
use think\annotation\Route;
use think\annotation\route\Validate;

class Course extends BaseController
{
    /**
     * 课程列表
     */
    public function courseList()
    {
        $param = [];
        //$param['page'] = input('page', '', 1);
        $param['page_size']= input('pageSize',10, 'int');
        $param['status'] = input('status', '-1', 'int');
        $param['weeks'] = input('weeks', '', 'int');
        $param['grade'] = input('grade', '', 'int');
        $param['course_name'] = input('courseName', '', 'string');
        $result = CourseService::courseList($param);

        return json_ok($result);
    }

    /**
     * 增加或修改课程
     * @Validate(VC::class,scene="save",batch="true")
     */
    public function courseAddOrUpdate()
    {
        $data['course_name'] = input('post.courseName', '', 'string');
        $data['status'] = input('post.status', '', 'int');
        $data['weeks'] = input('post.weeks', '', 'string');
        $data['start_time'] = input('post.startTime', '', 'string');
        $data['end_time'] = input('post.endTime', '', 'string');
        $data['class_place'] = input('post.classPlace', '', 'string');
        $data['grade'] = input('post.grade', '', 'string');
        $data['course_id'] = input('post.courseId', '', 'int');
        $data['course_type'] = input('post.courseType', '', 'int');
        $data['weeks'] = str_replace('，', ',', $data['weeks']);
        $data['grade'] = str_replace('，', ',', $data['grade']);

        $result = CourseService::addOrUpdate($data);
        return json_ok($result);
    }

    /**
     * 删除课程
     */
    public function courseDelete()
    {
        $courseId = input('post.courseId', '', 'int');
        if (!$courseId) return json_error(10002);
        $result = CourseService::courseDelete($courseId);

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
