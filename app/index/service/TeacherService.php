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
     * 班主任查看课程情况
     * @param string $teacherId 班主任ID
     * @return json
     */
    public static function course($user)
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
            if(strpos($v['weeks'], $weekday) != false){
                $todayCourses[] = $v;
            }
        }

        foreach ($todayCourses as $k2 => $v2) {
            # code...
            if($v2['start_time']){

            }
            $todayCourses[$k]['nums'] = Student::where('course_id','like','%'.$v2['course_id'].'%')->count();
        }
        return $todayCourses;

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
