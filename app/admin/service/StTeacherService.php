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

        if (!empty($param['teacherInfo'])) {
            $where .= ' AND (teacher_name like "%'. $param['teacherInfo']. '%" OR mobile like "%'. $param['teacherInfo']. '%" OR user_id like "%'. $param['teacherInfo']. '%")';
        }


        if (!empty($param['grade'])) {
            $where .= ' AND FIND_IN_SET('.$param['grade'].',grade)';
        }

        if (!empty($param['courseId'])) {
            $where .= ' AND FIND_IN_SET('.$param['courseId'].',course_id)';
        }

        $result = StTeacher::alias('t')
            ->where($where, $bind)
            ->field('teacher_id teacherId,teacher_name teacherName,mobile,grade,course_id courseId')
            ->paginate($param['page_size'])->toArray();

        $courseInfo = Course::where('status=:status', ['status' => Course::STATUS_UP])->column('course_name','course_id');
        foreach ($result['data'] as $k => $v) {
            $courseStr = '';
            if (isset($v['courseId'])) {
                if (strpos($v['courseId'], ',') !== false) {
                    $courseArr = explode(',', $v['courseId']);
                    foreach ($courseArr as $vv) {
                        if (isset($courseInfo[$vv])) {
                            $courseStr .= $courseInfo[$vv] . '、';
                        }
                    }
                    $courseStr = mb_substr($courseStr, 0, -1);
                } else {
                    if (isset($courseInfo[$v['courseId']])) {
                        $courseStr .= $courseInfo[$v['courseId']];
                    }
                }

            }
            $result['data'][$k]['course'] = $courseStr;
            $result['data'][$k]['gradeName'] = str_replace(',', '、', $v['grade']) . '年级';
        }

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
            $stTeach->head_image = '';
        } else {
            $stTeach = StTeacher::where('teacher_id=:teacher_id', ['teacher_id' => $data['teacherId']])->find();
            if (empty($stTeach)) {
                throw new MyException(10004);
            }
        }

        $courseArr = explode(',', $data['course_id']);
        $courseInfo = Course::select($courseArr);
        if (count($courseArr) != count($courseInfo)) {
            throw new MyException(10004);
        }

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
    public static function stTeacherDelete($teacherId){
        $oneTeacher = StTeacher::find($teacherId);
        if (empty($oneTeacher)) {
            throw new MyException(10004);
        }

        try {
            $oneTeacher->delete();
        } catch (\Exception $e) {
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }



}
