<?php
namespace app\index\controller;

use app\index\BaseController;
use think\Request;
use app\index\service\TeacherService;
use app\common\model\TuanTeacher;

class StTeacher extends BaseController
{

    /**
     * 社团老师排课计划
     * @param  Request $request
     * @return json
     */
    public function course(Request $request)
    {
        echo 123;die;
        dd($request->user);
        $userInfo = TuanTeacher::where($request->user['user_id'])->find();
        if(!$userInfo){
            return json_error(11104);
        }

        $teacherId = $request->param('teacherId');
        $action = $request->param('action'); //1.进行中 2.未开始
        if(empty($teacherId)){
            return json_error(100,'请传入班主任ID');
        }
        if(empty($action) || !in_array($action,[1,2])){
            return json_error(100,'参数错误');
        }
        return TeacherService::course($teacherId,$action);
    }

    /**
     * 班主任查看某个学生课程记录详情
     * @param  Request $request
     * @return json
     */
    public function courseByStudentDetails(Request $request)
    {
        $studentId = $request->param('studentId');

        if(empty($studentId)){
            return json_error(100,'请传入学生ID');
        }

        return TeacherService::courseByStudentDetails($studentId);
    }

}
