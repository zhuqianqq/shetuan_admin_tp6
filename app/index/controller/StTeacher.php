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
        return json_ok($ret_data);
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
        
        return json_ok($courses);
    }

    /**
     * 社团所有课程
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
        
        return json_ok($courses);
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
