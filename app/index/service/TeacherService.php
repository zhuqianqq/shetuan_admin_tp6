<?php
declare (strict_types=1);

namespace app\index\service;


use app\index\util\JwtUtil;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\index\model\BaseModel;

/**
 * 班主任
 * Class TeacherService
 * @package app\service
 */
class TeacherService
{


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
