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
     * @Route("getEateryFoods", method="POST")
     */
    public function getEateryFoods()
    {
        $result = F::getInfo();
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
     * 删除菜品
     * @Route("delete", method="GET")
     * @Validate(VF::class,scene="delete",batch="true")
     */
    public function delete()
    {
        $food_id = input('param.food_id', '', 'int');
        if (!$food_id) return json_error('14001');
        $result = F::deleteFood($food_id);

        return json_ok($result);
    }
}
