<?php
declare (strict_types=1);
namespace app\admin\service;

use app\admin\model\BaseModel;
use app\common\model\ClassModel;
use app\common\model\Course;
use app\admin\MyException;
use app\common\model\StTeacher;
use app\common\model\Student;
use app\common\model\SysUser;
use app\common\model\Teacher;
use app\common\model\TuanTeacher;
use app\common\model\User;

/**
 * 学生
 * Class TeacherService
 * @package app\service
 * @author  2066362155@qq.com
 */
class StudentService
{

    /**
     * 学生列表
     */
    public static function studentList($param)
    {
        $where = '1=1 ';
        $bind = [];

        if (!empty($param['teacherInfo'])) {
            $where .= ' AND (teacher_name like "%'. $param['teacherInfo']. '%" OR mobile like "%'. $param['teacherInfo']. '%" OR user_id like "%'. $param['teacherInfo']. '%")';
        }


        if (!empty($param['classId'])) {
            $where .= ' AND s.class_id=:class_id';
            $bind['class_id'] = $param['classId'];
        }

        if (!empty($param['grade'])) {
            $where .= ' AND grade=:grade';
            $bind['grade'] = $param['grade'];
        }

        $result = Student::alias('s')
            ->join(ClassModel::$_table . ' c', 's.class_id=c.class_id')
            ->where($where, $bind)
            ->field('student_id studentId,student_num studentNum,mobile,student_name studentName,s.class_id classId,parent_name parentName,address,grade,class_name')
            ->paginate($param['page_size'])->toArray();

        return $result;
    }

    /**
     * 更新或者创建学生
     * @param array $data
     * @return array 对象数组
     */
    public static function addOrUpdate($data)
    {

        if (empty($data['student_id'])) {//新增
                $student = new Student();
        } else {
            $student = Student::where('student_id=:student_id', ['student_id' => $data['student_id']])->find();
            if (empty($student)) {
                throw new MyException(10004);
            }
        }

        $student->student_num = $data['student_num'];
        $student->student_name = $data['student_name'];
        $student->parent_name = $data['parent_name'];
        $student->mobile = $data['mobile'];
        $student->address = $data['address'];
        $student->class_id = $data['class_id'];
        $student->school_id = 1;
        $student->parent_id = '';

//print_r($student->toArray());die;
        try {
            $student->save();
        } catch (\Exception $e){
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

    /**
     * 删除学生
     * @param array $data
     */
    public static function studentDelete($studentId){
        $studentId = Student::find($studentId);
        if (empty($studentId)) {
            throw new MyException(10004);
        }

        try {
            $studentId->delete();
        } catch (\Exception $e) {
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

}
