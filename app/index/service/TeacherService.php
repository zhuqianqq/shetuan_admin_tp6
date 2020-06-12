<?php
declare (strict_types=1);

namespace app\index\service;


use app\index\util\JwtUtil;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\index\model\BaseModel;
use app\common\model\ClassModel;
use app\common\model\Message;
use app\common\model\Course;
use app\common\model\Student;
use app\common\model\RollCall;
use app\common\model\TuanTeacher;

/**
 * 班主任
 * Class TeacherService
 * @package app\service
 */
class TeacherService
{
    /**
     * 认领班级列表
     * @param  $user 老师信息   $grade 年级信息
     * @return json
     */
    public static function allClasses($user,$grade)
    {
        
        $classes = ClassModel::where(['school_id'=>$user['school_id'],'grade'=>$grade,'enable'=>1])
                  ->field('class_id,class_name')
                  ->select()->toArray();

        foreach ($classes as $k => $v) {
            # code...
            if(strpos($user['class_id'],(string)$v['class_id']) != false){
                $classes[$k]['isChecked'] = 1;
            }else{
                $classes[$k]['isChecked'] = 0;
            }
        }
        return $classes;
    }
    
    /**
     * 认领班级操作
     * @param  $user 老师信息   $grade 年级信息
     * @return json 
     */
    public static function claimClass($user,$class_id)
    {
       return Message::insert([
            'school_id' =>$user['school_id'],
            'teacher_id' =>$user['teacher_id'],
            'teacher_type' =>1,
            'teacher_name' =>$user['teacher_name'],
            'contact' =>$user['mobile'],
            'status' => 0,
            'position' => '班主任',
            'ids' => $class_id
        ]);

    }


    /**
     * 班主任查看课程情况(首页)
     * @param string $user 用户信息
     * @return json
     */
    public static function course($user)
    {
       //查询数据库中对应老师的学生所报班的ids start
       $course_ids_arr = self::getClassIds($user);
        
       if(!$course_ids_arr){
            return [];
       }
       //end
        
       $courseInfo = Course::where('course_id','in',$course_ids_arr)
                      ->where('status',1)
                      ->whereTime('end_time','>',time())
                      ->select()->order('start_time','asc')->toArray();
                   
        if(!$courseInfo){
            return [];
        }

        $todayCourses = [];
        $weekday = date("w", time());
        foreach ($courseInfo as $k => $v) {
            if(strpos($v['weeks'], $weekday) != false){
                $todayCourses[] = $v;
            }
        }

        foreach ($todayCourses as $k2 => $v2) {
            # code...
            $startTime = strtotime(date('Y-m-d'). ' ' .$v2['start_time']);
            //课程状态  1:未开始  2：点名中 3.进行中 
            if(time()<$startTime-600){

                $todayCourses[$k2]['course_state'] = 1;
                $todayCourses[$k2]['course_state_text'] = '课程未开始';
                $todayCourses[$k2]['nums'] = Student::where('course_id','like','%'.$v2['course_id'].'%')->count();
                $todayCourses[$k2]['yidao'] = 0;
                $todayCourses[$k2]['weidao'] = 0;

            }else if(time()>$startTime-600 && time()<$startTime){

                $todayCourses[$k2]['course_state'] = 2;
                $todayCourses[$k2]['course_state_text'] = '课程点名中';
                $todayCourses[$k2]['nums'] = Student::where('course_id','like','%'.$v2['course_id'].'%')->count();
                $todayCourses[$k2]['yidao'] = 0;
                $todayCourses[$k2]['weidao'] = 0;

            }else{
                //var_dump($todayCourses);die;
                $todayCourses[$k2]['course_state'] = 3;
                $todayCourses[$k2]['course_state_text'] = '课程进行中';
                $rollCall = RollCall::where('course_id',$v2['course_id'])
                            ->where('school_id',$user['school_id'])
                            ->whereTime('create_time','today')
                            ->select();

                $todayCourses[$k2]['nums'] = Student::where('course_id','like','%'.$v2['course_id'].'%')->count();

                $yidao = $weidao = 0; //已到人数 请假人数
                foreach ($rollCall as $k3 => $v3) {
                    if($v3['status'] == 2){
                        $yidao++;
                    }

                    if($v3['status'] == 3 || $v3['status'] == 1){
                        $weidao++;
                    }
                }
                $todayCourses[$k2]['yidao'] = $yidao;
                $todayCourses[$k2]['weidao'] = $weidao;
                
            }
            
        }
        return $todayCourses;

    }

    /**
     * 课程情况列表
     * @param string $user 用户信息
     * @return json
     */
    public static function courseInfo($user)
    {
        $action = request()->param('action',1); //1.进行中 2.已结束
        $nowTime = date('H:i:s',time());

        //查询数据库中对应老师的学生所报班的ids start
        $course_ids_arr = self::getClassIds($user);
        
        if(!$course_ids_arr){
            return [];
        }
        //end
        if($action == 1){

            $courseInfo = Course::where('course_id','in',$course_ids_arr)
                      ->field('course_id,course_name,start_time,end_time,class_place,weeks')
                      ->where('status',1)
                      ->where('start_time', '<', $nowTime)
                      ->where('end_time', '>=', $nowTime)
                      ->order('start_time','asc')->select()->toArray();

        }else{

            $courseInfo = Course::where('course_id','in',$course_ids_arr)
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
            if(strpos($v['weeks'], $weekday) != false){
                $todayCourses[] = $v;
            }
        }

        foreach ($todayCourses as $k2 => $v2) {

                $tuanTeacher = TuanTeacher::where('course_id','like','%'.$v2['course_id'].'%')
                                ->field('teacher_name,mobile')
                                ->select();

                if($action == 1){
  
                        $todayCourses[$k2]['tuanTeacher'] = $tuanTeacher;

                        $todayCourses[$k2]['nums'] = Student::where('course_id','like','%'.$v2['course_id'].'%')->count();

                        $rollCall = RollCall::field('course_id,course_name,student_id,student_name,status')
                                    ->where('course_id',$v2['course_id'])
                                    ->where('school_id',$user['school_id'])
                                    ->whereTime('create_time','today')
                                    ->select();

                        $todayCourses[$k2]['rollCall'] = $rollCall;

                        $yidao = $weidao = 0; //已到人数 请假人数
                        foreach ($rollCall as $k3 => $v3) {
                            if($v3['status'] == 2){
                                $yidao++;
                            }

                            if($v3['status'] == 3 || $v3['status'] == 1){
                                $weidao++;
                            }
                        }
                        $todayCourses[$k2]['yidao'] = $yidao;
                        $todayCourses[$k2]['weidao'] = $weidao;
                        $todayCourses[$k2]['studentList'] = [];

                }else{

                      $todayCourses[$k2]['tuanTeacher'] = $tuanTeacher;
                      $todayCourses[$k2]['rollCall'] = [];
                      $todayCourses[$k2]['nums'] = 0;
                      $todayCourses[$k2]['yidao'] = 0;
                      $todayCourses[$k2]['weidao'] = 0;
                      $todayCourses[$k2]['studentList'] = Student::where('course_id','like','%'.$v2['course_id'].'%')
                                                          ->field('student_id,student_num,student_name')
                                                          ->select();

                }

                
        }

         return $todayCourses; 

    }
    /**
     * 获取对应老师下的对应学生所报班的ids
     * @param string $studentId 学生ID
     * @return json
     */
    public static function getClassIds($user)
    {
        $course_ids  = Student::where('class_id',$user['class_id'])
                ->where('school_id',$user['school_id'])
                ->Distinct(true)
                ->column('course_id');

        $course_ids_arr = [];

        foreach ($course_ids as $key => $val) {
            $temp = [];
            if(strpos($val, ',') != false){
                 $temp = explode(',', $val);
                 foreach ($temp as $key2 => $val2) {
                     $course_ids_arr[] = $val2;
                 }
            }else{
                $course_ids_arr[] = $val;
            }
        }

       $course_ids_arr = array_unique($course_ids_arr);
       return $course_ids_arr;
    }



    /**
     * 班主任查看某个学生课程记录详情
     * @param string $studentId 学生ID
     * @return json
     */
    public static function courseByStudentDetails($studentId)
    {
        //查看学生对应的社团信息

        $info = Db::table('st_student')->alias('sst')->leftJoin('st_shetuan ssh','sst.course_id = ssh.course_id')
            ->field('sst.student_name as studentName,sst.student_num as studentNum,ssh.course_name as courseName,ssh.course_type as courseType,ssh.class_place as classPlace,ssh.start_time as startTime,ssh.weeks,ssh.course_id as courseId')
            ->where('sst.student_id',$studentId)->find();

        $info['data'] = Db::table('st_roll_call')->where('student_id',$studentId)->where('course_id',$info['courseId'])->order('create_time','desc')->select()->toArray();
        return json_ok($info, 0);
    }


}
