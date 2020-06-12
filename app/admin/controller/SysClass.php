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
        $param['studentInfo'] = input('studentInfo', '', 'string');
        $param['grade'] = input('grade', '', 'int');
        $param['classId'] = input('classId', '', 'int');
        return SysClassService::getSysClassList($param);
    }

    /**
     * 课程
     */
    public function classInfo()
    {
        $result = SysClassService::classInfo();
        return json_ok($result);
    }

    /**
     * 增加或修改社团老师
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
     * 删除社团老师
     */
    public function classDelete()
    {
        $classId = input('post.classId', '', 'string');
        if (!$classId) return json_error(10002);
        $result = SysClassService::sysClassDelete($classId);

        return json_ok($result);
    }
}
