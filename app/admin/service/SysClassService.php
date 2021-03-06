<?php
declare (strict_types=1);

namespace app\admin\service;

use app\admin\MyException;
use app\admin\util\JwtUtil;
use app\common\model\ClassModel;
use app\common\model\Student;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\admin\model\BaseModel;
use \app\common\model\Teacher;

/**
 * 班级
 * Class SysClassService
 * @package app\service
 */
class SysClassService
{
    /**
     * 班级列表
     * @param array $param 参数数组
     * @return json
     */
    public static function getSysClassList($param)
    {

        $where = '1=1 AND enable=1';
        $bind = [];

        if (!empty($param['grade'])) {
            $where .= ' AND grade=:grade';
            $bind['grade'] = $param['grade'];
        }

        if (!empty($param['className'])) {
            $where .= ' AND class_name=:class_name';
            $bind['class_name'] = $param['className'];
        }

        $result = ClassModel::alias('c')
            ->leftJoin(Student::$_table . ' s', 's.class_id=c.class_id')
            ->where($where, $bind)
            ->field('c.class_id classId,class_name className,grade,count(student_id) studentNum,grade')
            ->group('grade,c.class_id')
            ->paginate($param['pageSize'])->toArray();

        foreach ($result['data'] as $k => $v) {
            if (!empty($v['classId'])) {
                //查找班主任
                $where1 = 'is_headmaster = 1 AND FIND_IN_SET(' . $v['classId'] . ',class_id)';
                $teacher_name = Teacher::where($where1)->value('teacher_name');
            }
            $result['data'][$k]['teacherName'] = $teacher_name ?? '';
        }

        return $result;
    }


    /**
     * 获取所有班级信息
     * @return array
     */
    public static function classInfo()
    {
        $res = ClassModel::where('enable', 1)->field('grade,class_id,class_name')->select();
        if (empty($res)) {
            return [];
        }

        return $res;
    }

    /**
     * 根据年级获取所有没有班主任的班级
     * @return array
     */
    public static function getClassByGrade($grade, $teacherId = '')
    {
        $where = 'enable=:enable';
        $bind['enable'] = 1;
        if ($grade) {
            $where .= ' and grade=:grade';
            $bind['grade'] = $grade;
        }
        $res = ClassModel::where($where, $bind)->field('class_id,class_name')->select();
        if (empty($res)) {
            return [];
        }


        $teacherInfo = Teacher::where('is_headmaster',1)->column('class_id','teacher_id');
        $str = implode(',', $teacherInfo);
        $classArr = array_unique(explode(',',$str));
        //当前班主任对应的班级
        if (!empty($teacherId)) {
            $currentTeacherClass = explode(',', $teacherInfo[$teacherId]);
        }

        $res = $res->toArray();
        foreach ($res as $k => $v) {
            if (!empty($currentTeacherClass) && in_array($v['class_id'], $currentTeacherClass)) {
                continue;
            }
            if (in_array($v['class_id'], $classArr)) {
                unset($res[$k]);
            }
        }

        return array_values($res);
    }


    /**
     * 根据年级获取所有班级
     * @return array
     */
    public static function getClassByGradeId($grade)
    {
        $where = 'enable=:enable';
        $bind['enable'] = 1;
        if ($grade) {
            $where .= ' and grade=:grade';
            $bind['grade'] = $grade;
        }
        $res = ClassModel::where($where, $bind)->field('class_id,class_name')->select();
        if (empty($res)) {
            return [];
        }

        return $res;
    }

        /**
     * 根据班级id获取年级
     * @param $classId 班级id
     */
    public static function getGradeByClass($classId)
    {
        $res = ClassModel::where('class_id=:class_id', ['class_id' => $classId])->field('class_id classId,class_name className,grade,enable')->find();
        return $res;
    }


    /**
     * 班级新增或修改
     * @return json
     */
    public static function addOrUpdate($data)
    {
        if (empty($data['class_id'])) {//新增
            $oneClass = ClassModel::where('grade=:grade and class_name=:class_name and enable=1', ['grade' => $data['grade'], 'class_name' => $data['class_name']])->find();
            if (!empty($oneClass)) {
                throw new MyException(10017);
            }
            $class = new ClassModel();
        } else {
            $oneClass = ClassModel::where('class_id!=:class_id and grade=:grade and class_name=:class_name and enable=1', ['class_id' => $data['class_id'], 'grade' => $data['grade'], 'class_name' => $data['class_name']])->find();
            if (!empty($oneClass)) {
                throw new MyException(10017);
            }
            $class = ClassModel::where('class_id=:class_id', ['class_id' => $data['class_id']])->find();
            if (empty($class)) {
                throw new MyException(10004);
            }
        }

        $class->class_name = $data['class_name'];
        $class->grade = $data['grade'];
        $class->school_id = 1;

        try {
            $class->save();
        } catch (\Exception $e) {
            throw new MyException(10001, $e->getMessage());
        }

        return (object)[];
    }

    /**
     * 班级删除
     * @param string $sysUserId 管理员ID
     * @return json
     */
    public static function sysClassDelete($sysClassId)
    {

        $where = 'is_headmaster = 1 AND FIND_IN_SET(' .$sysClassId . ',class_id)';
        $hasTeacher = Teacher::where($where)->find();
        if (!empty($hasTeacher)) {
            throw new MyException(10018);
        }

        BaseModel::beginTrans();
        try {
            if (strpos($sysClassId, ',') !== false) {

                ClassModel::where('class_id', 'in', $sysClassId)->update(['enable' => '2']);
            } else {
                ClassModel::where('class_id', $sysClassId)->update(['enable' => '2']);
            }
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(100, $e->getMessage());
        }
        BaseModel::commitTrans();
        return (object)[];
    }

    /**
     * 班级编辑
     * @param array $param 参数数组
     * @return json
     */
    public static function sysClassUpdate($param)
    {
        $isSysClassId = Db::table('st_class')->alias('sc')->where('class_id', $param['sysClassId'])->find();
        if (empty($isSysClassId)) {
            return json_error(100, '记录不存在');
        }
        BaseModel::beginTrans();
        try {
            $data = [];
            $data['class_name'] = $param['class_name'];
            $data['enable'] = $param['enable'];
            $data['grade'] = $param['grade'];
            Db::name('st_class')->where('class_id', $param['sysClassId'])->data($data)->update();
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(100, $e->getMessage());
        }
        BaseModel::commitTrans();
        return json_ok((object)array(), 200);
    }

    /**
     * 班级新增
     * @param array $param 参数数组
     * @return json
     */
    public static function sysClassAdd($param)
    {

        $isClass = Db::table('st_class')->alias('sc')->where('class_name', $param['className'])->find();
        if ($isClass && $isClass['enable'] == 1) {
            return json_error(100, '班级名称已存在');
        }
        BaseModel::beginTrans();
        try {
            $data = [];
            $data['class_name'] = $param['className'];
            $data['grade'] = $param['grade'];

            Db::name('st_class')->save($data);
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(100, $e->getMessage());
        }
        BaseModel::commitTrans();
        return json_ok((object)array(), 200);
    }
}
