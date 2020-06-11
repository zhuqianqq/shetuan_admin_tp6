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
        $param['page'] = $request->param('page', 1);
        $param['pageSize'] = $request->param('pageSize', 10);
        $param['studentInfo'] = $request->param('condition');
        return SysClassService::getSysClassList($param);
    }

    /**
     * 班级详情
     * @param  Request $request
     * @return json
     */
    public function sysClassDetails(Request $request)
    {
        $sysClassId = $request->param('sysClassId'); //班级ID
        if (empty($sysClassId)) {
            return json_error(100, '请传入班级ID');
        }
        return SysClassService::sysClassDetails($sysClassId);
    }

    /**
     * 班级删除
     * @param  Request $request
     * @return json
     */
    public function sysClassDelete(Request $request)
    {
        $sysClassId = $request->param('sysClassId'); //班级ID
        if (empty($sysClassId)) {
            return json_error(100, '请传入班级ID');
        }
        return SysClassService::sysClassDelete($sysClassId);
    }
    /**
     * 班级修改
     * @param  Request $request
     * @return json
     */
    public function sysClassUpdate(Request $request)
    {
        $param = [];
       // $param['nowUserId'] = $request->param('nowUserId'); //系统登录ID
        $param['sysClassId'] = $request->param('sysClassId'); //班级ID
        $param['class_name'] = $request->param('class_name');//班级名称
        $param['grade'] = $request->param('grade');//年纪
//        if(empty( $param['nowUserId'])){
//            return json_error(100, '请传入系统当前登录的用户ID');
//        }
        if (empty($param['sysUserId'])) {
            return json_error(100, '请传入账户ID');
        }
        return SysClassService::sysClassUpdate($param);
    }

    /**
     * 班级增加
     * @param  Request $request
     * @return json
     */
    public function sysClassAdd(Request $request)
    {
        $param = [];
        $param['className'] = $request->param('className');//班级名称
        $param['grade'] = $request->param('grade');//年级

        if(empty($param['grade']) || empty($param['className'])){
            return json_error(100, '缺少参数');
        }
        return SysClassService::sysClassAdd($param);
    }
}
