<?php

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\MyException;
use app\admin\service\CourseService;
use think\Request;
use app\admin\service\SysUserService;

class Course extends BaseController
{
    /**
     * 课程列表
     */
    public function courseList()
    {
        $param = [];
        $param['page'] = input('page', '', 1);
        $param['pageSize'] = input('pageSize', '', 10);
        $param['status'] = input('status', '-1', 'int');
        $param['weeks'] = input('weeks', '', 'int');
        $param['grade'] = input('grade', '', 'int');
        $param['course_name'] = input('courseName', '', 'string');
        $result = CourseService::courseList($param);
        return json_ok($result);
    }

    /**
     * 增加或修改课程
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
    public function delete()
    {
        $food_id = input('param.food_id', '', 'int');
        if (!$food_id) return json_error('14001');
        $result = CourseService::deleteFood($food_id);

        return json_ok($result);
    }

    /**
     * 课程下架
     */
    public function courseDrop()
    {
        $courseId = input('post.courseId', '', 'int');
        if (!$courseId) return json_error(10002);
        $result = CourseService::courseDrop($courseId);
        return json_ok($result);
    }
}
