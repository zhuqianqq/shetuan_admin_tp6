<?php

namespace app\admin\controller;

use app\admin\BaseController;
use think\Request;
use app\admin\service\SysClassService;

class SysClass extends BaseController
{
    /**
     * 班级列表
     * @param  Request $request
     * @return json
     */
    public function getSysClassList(Request $request)
    {
        $param = [];
        $param['page'] = input('page', 1);
        $param['pageSize'] = input('pageSize', 10);
        $param['grade'] = input('grade', '', 'int');
        $param['className'] = input('className', '', 'string');
        $resutlt = SysClassService::getSysClassList($param);

        return json_ok($resutlt);
    }

    /**
     * 班级信息
     */
    public function classInfo()
    {
        $result = SysClassService::classInfo();
        return json_ok($result);
    }

    /**
     * 根据年级获取所有没有班主任的班级
     */
    public function getClassByGrade()
    {
        $grade = input('grade', '', 'int');
        $teacherId = input('teacherId', '', 'int');
        if (!$grade) return json_error(10002);
        $result = SysClassService::getClassByGrade($grade, $teacherId);
        return json_ok($result);
    }


    /**
     * 根据年级获取班级
     */
    public function getClassByGradeId()
    {
        $grade = input('grade', '', 'int');
        if (!$grade) return json_error(10002);
        $result = SysClassService::getClassByGradeId($grade);
        return json_ok($result);
    }

    /**
     * 根据班级获取年级
     */
    public function getGradeByClass()
    {
        $classId = input('classId', '', 'int');
        if (!$classId) return json_error(10002);
        $result = SysClassService::getGradeByClass($classId);
        return json_ok($result);
    }


    /**
     * 增加或修改班级
     */
    public function classAddOrUpdate()
    {
        $data['class_name'] = input('post.className', '', 'string');
        $data['grade'] = input('post.grade', '', 'string');
        $data['class_id'] = input('post.classId', '', 'string');

        $result = SysClassService::addOrUpdate($data);
        return json_ok($result);
    }

    /**
     * 删除班级
     */
    public function classDelete()
    {
        $classId = input('post.classId', '', 'string');
        if (!$classId) return json_error(10002);
        $result = SysClassService::sysClassDelete($classId);

        return json_ok($result);
    }
}
