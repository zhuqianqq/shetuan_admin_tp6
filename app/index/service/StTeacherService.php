<?php
declare (strict_types=1);

namespace app\index\service;


use app\index\util\JwtUtil;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\index\model\BaseModel;
use app\common\model\Course;
use app\common\model\TuanTeacher;
use app\common\model\Student;
use app\common\model\RollCall;
use app\common\model\Message;
use app\common\model\Teacher;

/**
 * 社团老师
 * Class TeacherService
 * @package app\service
 */
class StTeacherService
{


    /**
     * 社团老师课程首页
     * @param string $user 社团老师信息
     * @return json
     */
    public static function index($user)
    {  
        $courseInfo = Course::where('course_id','in',$user['course_id'])
                      ->where('status',1)
                      ->whereTime('end_time','>',time())
                      ->select()->order('start_time','asc')->toArray();
        if(!$courseInfo){
            return [];
        }

        $todayCourses = [];
        $weekday = date("w", time());

        foreach ($courseInfo as $k => $v) {
            if(strpos($v['weeks'], $weekday) !== false){
                $todayCourses[] = $v;
            }
        }

        return $todayCourses;
    }


    /**
     * 社团老师排课计划
     * @param string $user 社团老师信息
     * @return json
     */
    public static function mySchedule($user)
    {
        $courseInfo = Course::where('course_id','in',$user['course_id'])
                      ->where('status',1)
                      ->field('course_id,course_name')
                      ->order('course_id','desc')->select()->toArray();
        if(!$courseInfo){
            return [];
        }

        foreach ($courseInfo as $k => $v) {
            $teacher_name = TuanTeacher::where('course_id','like','%'.$v['course_id'].'%')->column('teacher_name');
            $courseInfo[$k]['teacher_name'] = implode($teacher_name, ',');
            $courseInfo[$k]['nums'] = Student::where('course_id','like','%'.$v['course_id'].'%')->count();
            
        }

        return $courseInfo;
    }

     /**
     * 社团老师所有社团信息
     * @param string $user 社团老师信息
     * @return json
     */
    public static function allCourses($user)
    {
        $courseInfo = Course::where('status',1)
                      ->field('course_id,course_name')
                      ->order('course_id','desc')->select()->toArray();
        if(!$courseInfo){
            return [];
        }

        foreach ($courseInfo as $k => $v) {
            $teacher_name = TuanTeacher::where('course_id','like','%'.$v['course_id'].'%')->column('teacher_name');
            $courseInfo[$k]['teacher_name'] = implode($teacher_name, ',');
            $courseInfo[$k]['nums'] = Student::where('course_id','like','%'.$v['course_id'].'%')->count();
            if(strpos($user['course_id'],(string)$v['course_id']) !== false){
                $courseInfo[$k]['isChecked'] = 1;
            }else{
                $courseInfo[$k]['isChecked'] = 0;
            }
          
        }

        return $courseInfo;
    }


    /**
     * 认领社团操作
     * @param string $user 社团老师信息; $course_id 课程id  多课程用,号分隔
     * @return json
     */
    public static function claimCourses($user,$course_id)
    {
        
        // $_course_ids_str = $course_id . ',' . $user['course_id'];

        // $_course_ids_arr = explode(',', $_course_ids_str);

        // $course_ids_arr = array_unique(array_filter($_course_ids_arr)); //数组去空，去重

        // $course_ids_str = implode(',', $course_ids_arr);

        // return TuanTeacher::where('user_id',$user['user_id'])->update(['course_id'=>$course_ids_str]);
       
        return Message::insert([
            'school_id' =>'',
            'teacher_id' =>$user['teacher_id'],
            'teacher_type' =>2,
            'teacher_name' =>$user['teacher_name'],
            'contact' =>$user['mobile'],
            'status' => 0,
            'position' => '社团老师',
            'ids' => str_replace('，',',',$course_id)
        ]);
    }
    


    /**
     * 课程情况列表
     * @param string $user 社团老师信息
     * @return json
     */
    public static function courseInfoList($user)
    {
        $action = request()->param('action',1); //1.进行中 2.已结束
        $nowTime = date('H:i:s',time());

        if($action == 1){

            $courseInfo = Course::where('course_id','in',$user['course_id'])
                      ->field('course_id,course_name,start_time,end_time,class_place,weeks')
                      ->where('status',1)
                      ->where('start_time', '<', $nowTime)
                      ->where('end_time', '>=', $nowTime)
                      ->order('start_time','asc')->select()->toArray();

        }else{

            $courseInfo = Course::where('course_id','in',$user['course_id'])
                      ->field('course_id,course_name,start_time,end_time,class_place,weeks')
                      ->where('status',1)
                      ->where('end_time', '<', $nowTime)
                      ->order('end_time','asc')->select()->toArray();
        }
        
        if(!$courseInfo){
            return [];
        }

        $todayCourses = [];
        $weekday = date("w", time());
        foreach ($courseInfo as $k => $v) {
            if(strpos($v['weeks'], $weekday) !== false){
                $todayCourses[] = $v;
            }
        }

        if(!$todayCourses){
            return [];
        }

        foreach ($todayCourses as $k => $v) {
            
            $todayCourses[$k]['nums'] = Student::where('course_id','like','%'.$v['course_id'].'%')->count();

            $rollCall = RollCall::where('course_id',$v['course_id'])
            ->whereTime('create_time','today')
            ->select();

            $yidao = $qingjia = 0; //已到人数 请假人数
            foreach ($rollCall as $k2 => $v2) {
                if($v2['status'] == 2){
                    $yidao++;
                }

                if($v2['status'] == 3){
                    $qingjia++;
                }
            }

            $todayCourses[$k]['qingjiaNums'] = $qingjia;
            $todayCourses[$k]['weidaoNums'] =$todayCourses[$k]['nums'] - $yidao;
            
        }

       return $todayCourses;

    }



    /**
     * 社团详情接口
     * @param array $user 社团老师信息
     * @return array
     */
    public static function shetuanDetail($user,$course_id)
    {
        $courseInfo = Course::where('status',1)
                      ->where('course_id',$course_id)
                      ->find();
        if(!$courseInfo){
            return [];
        }

        $courseInfo['teacher_info'] = TuanTeacher::where('course_id','like','%'.$courseInfo['course_id'].'%')
                                    ->field('teacher_name,mobile')
                                    ->select();

        $courseInfo['student_list'] = Student::alias('s')
                                    ->leftJoin('st_class c','s.class_id = c.class_id')
                                    ->leftJoin('st_teacher t','s.class_id = t.class_id')
                                    ->where('s.course_id',$course_id)
                                    ->field('s.student_id,s.student_num,s.student_name,s.course_id,s.class_id,c.class_name,t.mobile,t.teacher_name')
                                    ->select()->toArray();  

        return $courseInfo; 
    }
    

    /**
     * 社团老师进行学生点名列表接口
     * @param array $user 社团老师信息
     * @return array
     */
    public static function callRollList($user,$course_id)
    {

        $courseInfo = Course::where('status',1)
                      ->where('course_id',$course_id)
                      ->find();
        if(!$courseInfo){
            return [];
        }

        $studentList = Student::alias('s')
                           ->leftJoin('st_class c','s.class_id = c.class_id')
                           ->where('s.course_id',$course_id)
                           ->field('s.student_id,s.student_num,s.student_name,s.course_id,s.class_id,c.class_name')
                           ->select()->toArray();  
        foreach ($studentList as $k => $v) {
            $teacher_info = Teacher::where('class_id','like','%'.$v['class_id'].'%')->column('teacher_name,mobile');
            $studentList[$k]['teacher_info'] = $teacher_info;
            $studentList[$k]['status'] = RollCall::where('course_id',$course_id)
                                        ->where('student_id',$v['student_id'])
                                        ->whereTime('create_time','today')
                                        ->value('status');
        }


        return ['studentList'=> $studentList,'course'=>$courseInfo];

    }


    /**
     * 社团老师进行学生点名操作 
     * @param array $user 社团老师信息 
     * @return array
     */
    public static function dianMing($user,$data)
    {
          $student = Student::alias('s')
                    ->leftJoin('st_shetuan st','s.course_id = st.course_id')
                    ->field('s.*,st.course_name,st.start_time,st.end_time')
                    ->where('s.student_id',$data['student_id'])
                    ->find();

          $isDianMing  =  RollCall::where('course_id',$data['course_id'])
                       ->where('student_id',$data['student_id'])
                       ->whereTime('create_time','today')
                       ->find();

          if(!$isDianMing){
            //新增操作
            return RollCall::insert([
                        'school_id' => $student['school_id'],
                        'course_id' => $data['course_id'],
                        'course_name' => $student['course_name'],
                        'student_id' => $student['student_id'],
                        'student_name' => $student['student_name'],
                        'student_num' => $student['student_num'],
                        'start_time' => $student['start_time'],
                        'end_time' => $student['end_time'],
                        'status' => $data['state'],
                        'create_time' => date('Y-m-d H:i:s',time())
                    ]);
          }else{
            //更新操作
            return RollCall::where('id',$isDianMing['id'])
                   ->update([
                        'status' => $data['state'],
                    ]);

          }
    }
}
