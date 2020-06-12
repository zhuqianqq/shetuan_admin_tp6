<?php
namespace app\index\controller;

use app\index\BaseController;
use think\Request;
use app\index\service\TeacherService;
use app\index\service\StTeacherService;
use app\common\model\TuanTeacher;
use app\common\model\Course;

class StTeacher extends BaseController
{

    /**
     * 社团老师排课首页
     * @param  Request $request
     * @return json
     */
    public function course(Request $request)
    {
        
        $userInfo = TuanTeacher::where('user_id',$request->st_user['user_id'])->find();
        if(!$userInfo){
            return json_error(11104);
        }
        $courses = StTeacherService::index($userInfo);
        $ret_data = [
            'teacher_name' => $userInfo['teacher_name'],
            'head_image' => $userInfo['head_image'],
            'courses' => $courses,
            'today' => date('Y-m-d',time())
        ];
        return json_ok($ret_data,0);
    }


    /**
     * 社团老师排课计划
     * @param  Request $request
     * @return json
     */
    public function schedule(Request $request)
    {
        
        $userInfo = TuanTeacher::where('user_id',$request->st_user['user_id'])->find();
        if(!$userInfo){
            return json_error(11104);
        }
        $courses = StTeacherService::mySchedule($userInfo);
        
        return json_ok($courses,0);
    }

    /**
     * 认领社团列表
     * @param  Request $request
     * @return json
     */
    public function allCourses(Request $request)
    {
        
        $userInfo = TuanTeacher::where('user_id',$request->st_user['user_id'])->find();
        if(!$userInfo){
            return json_error(11104);
        }
        $courses = StTeacherService::allCourses($userInfo);
        
        return json_ok($courses,0);
    }


    /**
     * 认领社团操作
     * @param  Request $request
     * @return json
     */
    public function claimCourses(Request $request)
    {
        $userInfo = TuanTeacher::where('user_id',$request->st_user['user_id'])->find();
        if(!$userInfo){
            return json_error(11104);
        }

        $course_id = $request->param('course_id',''); //社团id
        if(empty($course_id)){
            return json_error(100,'情传入社团课程id');
        }

        $res = StTeacherService::claimCourses($userInfo,$course_id);
        return json_ok($res,0);
    }
    /**
     * 课程情况列表
     * @param  Request $request
     * @return json
     */
    public function courseInfoList(Request $request)
    {
        $userInfo = TuanTeacher::where('user_id',$request->st_user['user_id'])->find();
        if(!$userInfo){
            return json_error(11104);
        }
        $courses = StTeacherService::courseInfoList($userInfo);

        $ret_data = [ 
            'courses' => $courses,
            'today' => date('Y-m-d',time())
        ];
        return json_ok($ret_data,0); 
    }


    /**
     * 社团详情接口
     * @param  Request $request
     * @return json
     */
    public function shetuanDetail(Request $request)
    {
        $userInfo = TuanTeacher::where('user_id',$request->st_user['user_id'])->find();
        if(!$userInfo){
            return json_error(11104);
        }

        $course_id = $request->param('course_id',''); //社团id
        if(empty($course_id)){
            return json_error(100,'情传入社团课程id');
        }

        $shetuan = StTeacherService::shetuanDetail($userInfo,$course_id);

        return json_ok($shetuan,0); 
    }

}
