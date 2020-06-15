<?php
namespace app\index\controller;

use app\index\BaseController;
use think\Request;
use app\index\service\TeacherService;
use app\common\model\Teacher as TModel;

class Teacher extends BaseController
{
   
    /**
     * 班主任课程情况&&缺省
     * @param  Request $request
     * @return json
     */
    public function course(Request $request)
    {
       
        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }
 
        $courses = TeacherService::course($userInfo);
        $ret_data = [
            'teacher_name' => $userInfo['teacher_name'],
            'head_image' => $userInfo['head_image'],
            'courses' => $courses,
            'today' => date('Y-m-d',time())
        ];
        return json_ok($ret_data,0);
    }


    /**
     * 课程情况列表
     * @param  Request $request
     * @return json
     */
    public function courseInfo(Request $request)
    {
       
        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }

        $courses = TeacherService::courseInfo($userInfo);

        $ret_data = [ 
            'courses' => $courses,
            'today' => date('Y-m-d',time())
        ];
        return json_ok($ret_data,0); 

    }


    /**
     * 学生信息列表
     * @param  Request $request
     * @return json
     */
    public function studentInfoList(Request $request)
    {
        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }

        $studentsList = TeacherService::studentInfoList($userInfo);

        return json_ok($studentsList,0); 

    }


    /**
     * 具体学生的上课情况
     * @param  Request $request
     * @return json
     */
    public function studentInfoDetail(Request $request)
    {
        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }

        $student_id = $request->param('student_id',''); //学生id
        if(empty($student_id)){
            return json_error(100,'请传入学生id');
        }

        $info = TeacherService::studentInfoDetail($userInfo,$student_id);

        return json_ok($info,0); 

    }


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

        return json_ok(TeacherService::claimClass($userInfo,$class_id),0);
    }

      /**
     * 学生报团操作
     * @param  Request $request
     * @return json
     */
    public function attendCourse(Request $request)
    {   
        
        $data = $request->param('data',''); //json数组参数 [{"student_id":"1","course_id":"3"},{"student_id":"8","course_id":"10"}]
        
        if(!$data){
            return json_error(100,'缺少参数！');
        }

        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }

        return json_ok(TeacherService::attendCourse($userInfo,$data),0);

    }


    /**
     * 给学生请假
     * @param  Request $request
     * @return json
     */
    public function askForLeave(Request $request)
    {   
        $student_id = $request->param('student_id',''); //学生id
        if(empty($student_id)){
            return json_error(100,'请传入学生id');
        }

        $userInfo = TModel::where('user_id',$request->st_user['user_id'])->find();

        if(!$userInfo){
            return json_error(11104);
        }

        return json_ok(TeacherService::askForLeave($userInfo,$student_id),0);
    }


    /**
     * 所有社团课程
     * @param  Request $request
     * @return json
     */
    public function allSheTuanCourses(Request $request)
    {   
        return json_ok(TeacherService::allSheTuanCourses(),0);
    }

}
