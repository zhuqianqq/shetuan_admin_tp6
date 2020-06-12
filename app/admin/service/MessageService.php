<?php
declare (strict_types=1);

namespace app\admin\service;

use app\admin\MyException;
use app\admin\util\JwtUtil;
use app\common\model\ClassModel;
use app\common\model\Message;
use app\common\model\StTeacher;
use app\common\model\Student;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\admin\model\BaseModel;
use \app\common\model\Teacher;

/**
 * 消息
 * Class MessageService
 * @package app\service
 */
class MessageService
{
    /**
     * 消息列表
     * @param array $param 参数数组
     * @return json
     */
    public static function getMessageList($param)
    {

        $result = Message::field('teacher_name teacherName,contact,status,position,ids,teacher_type')->select();
        if (empty($result)) {
            return [];
        }

        $result = $result->toArray();
        if ($result['teacher_type'] == 1) {
            $classOrSheTuanName = ClassModel::where('class_id=:class_id', ['class_id' => $result['ids']])->value();
        } else {
            $classOrSheTuanName = StTeacher::where('course_id=:course_id', ['course_id' => $result['ids']])->value();
        }

        return $result;
    }


    /**
     * 获取所有b班级信息
     * @return array
     */
    public static function classInfo()
    {
        $res = ClassModel::alias('t')
            ->leftJoin(Student::$_table . ' s', 's.class_id=c.class_id')
            ->field('class_id,class_name')
            ->select();

        if (empty($res)) {
            return [];
        }

        return $res;
    }

    /**
     * 班级新增或删除
     * @return json
     */
    public static function addOrUpdate($data)
    {
        if (empty($data['class_id'])) {//新增
            $class = new ClassModel();
        } else {
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
