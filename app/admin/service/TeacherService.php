<?php
declare (strict_types=1);
namespace app\admin\service;

use app\admin\model\BaseModel;
use app\common\model\ClassModel;
use app\common\model\Course;
use app\admin\MyException;
use app\common\model\StTeacher;
use app\common\model\SysUser;
use app\common\model\Teacher;
use app\common\model\TuanTeacher;
use app\common\model\User;

/**
 * 老师
 * Class TeacherService
 * @package app\service
 * @author  2066362155@qq.com
 */
class TeacherService
{

    /**
     * 课程列表
     */
    public static function teacherList($param)
    {
        $where = '1=1 ';
        $bind = [];

        if (!empty($param['teacherInfo'])) {
            $where .= ' AND (teacher_name like "%'. $param['teacherInfo']. '%" OR mobile like "%'. $param['teacherInfo']. '%" OR user_id like "%'. $param['teacherInfo']. '%")';
        }


        if (!empty($param['grade'])) {
            $where .= ' AND FIND_IN_SET('.$param['grade'].',grade)';
        }

        if (!empty($param['classId'])) {
            $where .= ' AND FIND_IN_SET('.$param['classId'].',class_id)';
        }

        $result = Teacher::alias('t')
            ->where($where, $bind)
            ->field('teacher_id teacherId,teacher_name teacherName,mobile,grade,class_id classId')
            ->paginate($param['page_size'])->toArray();

        $classInfo = ClassModel::column('class_name','class_id');

        foreach ($result['data'] as $k => $v) {
            //获取班级名称
            $classStr = '';
            if (strpos($v['classId'], ',') !== false) {
                $classArr = explode(',', $v['classId']);
                foreach ($classArr as $vv) {
                    $classStr .= $classInfo[$vv] . '、';
                }
                $classStr = mb_substr($classStr, 0, -1);
            } else
                $classStr .= $classInfo[$v['classId']];

            $result['data'][$k]['course'] = $classStr;

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
                $teacher = new Teacher();
        } else {
            $teacher = Teacher::where('teacher_id=:teacher_id', ['teacher_id' => $data['teacherId']])->find();
            if (empty($teacher)) {
                throw new MyException(10004);
            }
        }

        $teacher->teacher_name = $data['teacher_name'];
        $teacher->mobile = $data['mobile'];
        $teacher->grade = $data['grade'];
        $teacher->class_id = $data['class_id'];
        $teacher->is_headmaster = 1;
        $teacher->school_id = 1;

        try {
            $teacher->save();
        } catch (\Exception $e){
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

    /**
     * 删除课程
     * @param array $data
     */
    public static function teacherDelete($teacherId){
        $oneTeacher = Teacher::find($teacherId);
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
