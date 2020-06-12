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
            if(strpos($v['weeks'], $weekday) != false){
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
            if(strpos($user['course_id'],(string)$v['course_id']) != false){
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
            if(strpos($v['weeks'], $weekday) != false){
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

        return $courseInfo; 
    }
    



    /**
     * 班主任查看课程情况
     * @param string $teacherId 班主任ID
     * @return json
     */
    public static function course($teacherId, $action)
    {
        $model = Db::table('st_student')->alias('sst');
        if ($action == 1) {
            $model->where('ssh.start_time', '<', date('H:i:s', time()))
                ->where('ssh.end_time', '>', date('H:i:s', time()));
        } elseif ($action == 2) {
            $model->where('ssh.start_time', '>', date('H:i:s', time()));
        }
        $weekArray = array("日", "一", "二", "三", "四", "五", "六");
        $dateWeek = "星期" . $weekArray[date("w", time())];

        $isTeacher = Db::table('st_teacher')->where('teacher_id', $teacherId)->where('is_headmaster', 1)->find();
        if (!$isTeacher) {
            return json_error(100, '未存在记录');
        }
        $class = Db::table('st_class')->where('class_id', $isTeacher['class_id'])->find();
        if (!$class) {
            return json_error(100, '未找到所属班级');
        }

        $student = $model->field('sst.student_id as studentId,sst.student_num as studentNum,sst.student_name as studentName,ssh.course_id as courseId,ssh.course_name as courseName,ssh.weeks,stt.teacher_name as courseTeacherName,stt.mobile as courseTeacherMobile,ssh.start_time as startTime,ssh.end_time as endTime')
            ->join('st_shetuan ssh', 'sst.course_id = ssh.course_id')
            ->join('st_tuan_teacher stt', 'sst.course_id = stt.course_id')
            ->where('sst.class_id', $class['class_id'])
            ->where('ssh.weeks', $dateWeek)
            ->select()->toArray();

        $dateTime = date('Y-m-d 00:00:00', time());
        foreach ($student as $key => $value) {
            $student[$key]['status'] = Db::table('st_roll_call')->where('student_id', $value['studentId'])->where('create_time', '>', $dateTime)->value('status');
        }
        $res = [];
        foreach ($student as $key => $value) {
            $res[$value['courseName']][] = $value;
        }

        foreach ($res as $key => $value) {
            $status = array_column($value, 'status');
            array_multisort($status, SORT_ASC, $res[$key]);
        }
        $list = [];
        foreach ($res as $key => $value) {
            $list[$key]['info'] = Db::table('st_shetuan')->where('course_name',$key)->find();
            $list[$key]['data'] = $value;
        }
        return json_ok($list, 200);
//        $model = Db::table('sys_user')->alias('sy');
//        if (!empty($param['condition'])) {
//            $where = 'sy.account like "%' . $param['condition'] . '%" or sy.user_name like "%' . $param['condition'] . '%" or sy.mobile like "%' . $param['condition'] . '%"';
//            $model->where($where);
//        }
//        $res = $model->field('sy.user_id as userId,sy.account,sy.user_name as userName,sy.mobile,sy.user_type as userType,sy.school_id as schoolId,sy.create_time as createTime')
//            ->paginate(['page' => $param['page'], 'list_rows' => $param['pageSize']])->toArray();
//        if (empty($res)) {
//            return json_ok((object)array(), 0);
//        }
//        $list = ['total' => $res['total'], 'currentPage' => $res['current_page'], 'lastPage' => $res['last_page'], 'data' => $res['data']];
//        return json_ok($list, 0);
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
