<?php
declare (strict_types=1);
namespace app\admin\service;

use app\admin\model\BaseModel;
use app\common\model\Course;
use app\admin\MyException;
use app\common\model\StTeacher;

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
            $where .= ' AND  FIND_IN_SET('.$param['weeks'].',weeks)';
        }

        if (!empty($param['grade'])) {
            $where .= ' AND FIND_IN_SET('.$param['grade'].',grade)';
        }

        $result = Course::where($where, $bind)->paginate($param['page_size'])->toArray();;

        return $result;
    }


    /**
     * 获取所有课程信息
     * @return array
     */
    public static function courseInfo()
    {
        $res = Course::where('status', 1)->field('course_id courseId,course_name courseName,weeks,start_time startTime,end_time endTime,course_type courseType,class_place classPlace,grade')->select();
        if (empty($res)) {
            return [];
        }

        return $res->toArray();
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
    public static function courseDelete($coursId){
        $oneCourse = Course::find($coursId);
        if (empty($oneCourse)) {
            throw new MyException(10004);
        }

        //未下架不允许删除
        if ($oneCourse->status == Course::STATUS_UP) {
            throw new MyException(10015);
        }

        //有老师选择的课程不能删除
        $hasCourse = StTeacher::where('course_id=:course_id', ['course_id' => $coursId])->find();
        if (!empty($hasCourse)) {
            throw new MyException(10016);
        }

        try {
            $oneCourse->delete();
        } catch (\Exception $e) {
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

    /**
     * 上下架课程
     * @param array $data
     */
    public static function courseOnOrOff($coursId, $status){
        $oneCourse = Course::find($coursId);
        if (empty($oneCourse)) {
            throw new MyException(10004);
        }

        $oneCourse->status = $status;
        try {
            $oneCourse->save();
        } catch (\Exception $e) {
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

}
