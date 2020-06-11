<?php
declare (strict_types=1);
namespace app\admin\service;

use app\admin\model\BaseModel;
use app\common\model\Course;
use app\admin\MyException;

/**
 * 课程
 * Class FoodService
 * @package app\service
 * @author  2066362155@qq.com
 */
class CourseService
{

    /**
     * 课程列表
     */
    public static function courseList($param)
    {
        $where = '1=1 ';
        $bind = [];
        if ($param['status'] != -1) {
            $where .= 'AND status=:status';
            $bind = ['status' => $param['status']];
        }

        if (!empty($param['course_name'])) {
            $where .= ' AND course_name like "%'. $param['course_name']. '%"';
        }

        if (!empty($param['weeks'])) {
            $where .= ' AND weeks FIND_IN_SET('.$param['weeks'].',weeks)';
        }

        if (!empty($param['grade'])) {
            $where .= ' AND grade FIND_IN_SET('.$param['grade'].',grade)';
        }

        $result = Course::where($where, $bind)->select();

        return $result;
    }

    /**
     * 更新或者创建课程
     * @param array $data
     * @return array 对象数组
     */
    public static function addOrUpdate($data)
    {

        if (empty($data['course_id'])) {//新增
            $coursM = new Course();
        } else {
            $coursM = Course::where('course_id=:course_id', ['course_id' => $data['course_id']])->find();
            if (empty($coursM)) {
                throw new MyException(10004);
            }
        }

        $coursM->school_id = 1;
        $coursM->course_name = $data['course_name'];
        $coursM->status = $data['status'];
        $coursM->weeks = $data['weeks'];
        $coursM->start_time = $data['start_time'];
        $coursM->end_time = $data['end_time'];
        $coursM->class_place = $data['class_place'];
        $coursM->grade = $data['grade'];
        $coursM->course_type = $data['course_type'];

        try {
            $coursM->save();
        } catch (\Exception $e){
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

    /**
     * 删除课程
     * @param array $data
     */
    public static function deleteFood($foodId){
        $oneFood = F::find($foodId);
        if (!$oneFood) {
            throw new MyException(14002);
        }
        if (!$oneFood->delete()) {
            throw new MyException(14004);
        }

        return (object)[];
    }

    /**
     * 下架课程
     * @param array $data
     */
    public static function courseDrop($coursId){
        $oneCourse = CourseService::find($coursId);
        if (!$oneCourse) {
            throw new MyException(10004);
        }

        $oneCourse->status = 0;
        try {
            $oneCourse->save();
        } catch (\Exception $e) {
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

}
