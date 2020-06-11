<?php
declare (strict_types=1);
namespace app\admin\service;

use app\admin\model\BaseModel;
use app\common\model\Course;
use app\admin\MyException;
use app\common\model\StTeacher;
use app\common\model\SysUser;
use app\common\model\TuanTeacher;
use app\common\model\User;

/**
 * 社团老师
 * Class FoodService
 * @package app\service
 * @author  2066362155@qq.com
 */
class StTeacherService
{

    /**
     * 课程列表
     */
    public static function stTeacherList($param)
    {
        $where = '1=1 ';
        $bind = [];
        if ($param['courseId'] != -1) {
            $where .= 'AND course_id=:course_id';
            $bind = ['course_id' => $param['courseId']];
        }

        if (!empty($param['teacherInfo'])) {
            $where .= ' AND (teacher_name like "%'. $param['teacherInfo']. '%" OR mobile like "%'. $param['teacherInfo']. '%" OR user_id like "%'. $param['teacherInfo']. '%")';
        }


        if (!empty($param['grade'])) {
            $where .= ' AND FIND_IN_SET('.$param['grade'].',grade)';
        }

        $result = StTeacher::alias('t')
            ->join(Course::class .' c','c.course_id = t.course_id')
            ->where($where, $bind)
            ->field('teacher_name teacherName', '')
            ->paginate($param['page_size'])->toArray();;

        return $result;
    }

    /**
     * 更新或者创建课程
     * @param array $data
     * @return array 对象数组
     */
    public static function addOrUpdate($data)
    {

        if (empty($data['teacherId'])) {//新增
            $stTeach = new StTeacher();
        } else {
            $stTeach = StTeacher::where('teacher_id=:teacher_id', ['teacher_id' => $data['teacher_id']])->find();
            if (empty($stTeach)) {
                throw new MyException(10004);
            }
        }

        $courseArr = explode(',', $data['course_id']);
        $courseInfo = Course::select($courseArr);
        if (count($courseArr) != count($courseInfo)) {
            throw new MyException(10004);
        }
        $stTeach->user_id = '';
        $stTeach->head_image = '';
        $stTeach->teacher_name = $data['teacher_name'];
        $stTeach->mobile = $data['mobile'];
        $stTeach->grade = $data['grade'];
        $stTeach->course_id = $data['course_id'];

        try {
            $stTeach->save();
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
