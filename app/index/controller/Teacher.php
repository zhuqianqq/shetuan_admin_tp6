<?php
namespace app\index\controller;

use app\index\BaseController;
use think\Request;
use app\index\service\TeacherService;
use app\common\model\Teacher as TModel;

class Teacher extends BaseController
{
   
    // /**
    //  * 班主任课程情况&&缺省
    //  * @param  Request $request
    //  * @return json
    //  */
    // public function course(Request $request)
    // {
    //     $teacherId = $request->param('teacherId');
    //     $action = $request->param('action'); //1.进行中 2.未开始
    //     if(empty($teacherId)){
    //         return json_error(100,'请传入班主任ID');
    //     }
    //     if(empty($action) || !in_array($action,[1,2])){
    //         return json_error(100,'参数错误');
    //     }
    //     return TeacherService::course($teacherId,$action);
    // }

    // /**
    //  * 班主任查看某个学生课程记录详情
    //  * @param  Request $request
    //  * @return json
    //  */
    // public function courseByStudentDetails(Request $request)
    // {
    //     $studentId = $request->param('studentId');

    //     if(empty($studentId)){
    //         return json_error(100,'请传入学生ID');
    //     }

    //     return TeacherService::courseByStudentDetails($studentId);
    // }


    /**
     * 认领班级列表
     * @param  Request $request
     * @return json
     */
    public function allClasses(Request $request)
    {
  
        $grade = $request->param('grade',''); //年级
        if(empty($grade)){
            return json_error(100,'请传入年级信息');
        }

        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }

        $classes = TeacherService::allClasses($userInfo,$grade);

        $ret_data = [
            'teacher_name' => $userInfo['teacher_name'],
            'user_role' => 1,
            'classes' => $classes,
        ];

        return json_ok($ret_data,0);
    }


     /**
     * 认领班级操作
     * @param  Request $request
     * @return json
     */
    public function claimClass(Request $request)
    {
        $class_id = $request->param('class_id',''); //年级
        if(empty($class_id)){
            return json_error(100,'请传入班级id');
        }

        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }

        $classes = TeacherService::claimClass($userInfo,$class_id);

        $ret_data = [
            'teacher_name' => $userInfo['teacher_name'],
            'user_role' => 1,
            'classes' => $classes,
        ];

        return json_ok($ret_data,0);
    }
}
