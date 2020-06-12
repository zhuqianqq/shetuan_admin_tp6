<?php
declare (strict_types=1);

namespace app\admin\service;

use app\admin\MyException;
use app\admin\util\JwtUtil;
use app\common\model\ClassModel;
use app\common\model\Course;
use app\common\model\Message;
use app\common\model\StTeacher;
use app\common\model\Student;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\admin\model\BaseModel;
use \app\common\model\Teacher;

/**
 * 消息
 * Class MessageService
 * @package app\service
 */
class MessageService
{
    /**
     * 消息列表
     * @param array $param 参数数组
     * @return json
     */
    public static function getMessageList($param)
    {

        $result = Message::field('id,teacher_name teacherName,contact,status,position,ids,teacher_type')->order('id desc')->select();
        if (empty($result)) {
            return [];
        }

        $result = $result->toArray();
        foreach ($result as $k => $v) {
            $idArr = explode(',', $v['ids']);
            if ($v['teacher_type'] == 1) {
                $classOrCourseInfo = ClassModel::where([['class_id','in', $idArr]])->column('class_name');
            } else {
                $classOrCourseInfo = Course::where([['course_id','in', $idArr]])->column('course_name');
            }

            $classOrCourseName = count($classOrCourseInfo) ? implode($classOrCourseInfo, '、') : '';
            $result[$k]['classOrCourseName'] = $classOrCourseName;
        }

        $count = count($result);
        return ['count'=>$count, 'list' => $result];
    }

    /**
     * 消息审核
     * @param array $param 参数数组
     * @return json
     */
   public static function checkMessage($data)
   {
       $oneMessage = Message::where('id=:id', ['id' => $data['id']])->find();
       if (empty($oneMessage)) {
           throw new MyException(10004);
       }

       if ($oneMessage['status'] == 1 || $oneMessage['status']  == $data['status']) {
           throw new MyException(10004);
       }

       try {
           $oneMessage->status = $data['status'];
           $oneMessage->save();
       } catch (\Exception $e) {
           throw new MyException(10001, $e->getMessage());
       }

       return (object)[];
   }
}
