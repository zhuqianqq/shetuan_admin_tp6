<?php

namespace app\index\controller;

use app\index\MyException;
use app\common\model\Course;
use app\common\model\Student;
use app\common\model\RollCall;
use think\facade\Db;
use think\facade\Cache;

/**
 * 生成每天的点名表
 * Class Index
 */

class CronJob  {

	const CACHE_KEY = 'DianMingList:';

	public static function DianMingList() {

		$key = self::CACHE_KEY . date('Y-m-d',time());
		var_dump(Cache::set($key, 1, 7*86400));var_dump(Cache::get($key));die; //缓存7天时间


		try {

			$weekday = date("w", time());
	        $weekday==0 && $weekday=7;//如果是0 改为7

			 //今天的社团课程ids
			$todayCourses = Course::where('status',1)
						 ->where('weeks','like','%'.$weekday.'%')
						 ->column('course_id');

	        if(!$todayCourses){
	            return [];
	        }
	      
	        //找出参加今天课程的所有学生信息
    		$students = Student::alias('s')
    					->leftJoin('st_shetuan st','s.course_id = st.course_id')
    					->field('s.*,st.course_name,st.start_time,st.end_time')
    					->where('s.course_id','in',$todayCourses)
				 		->select()->toArray();


			$batch_data = [];
			$date_time = date('Y-m-d H:i:s',time());

	        foreach ($students as $k => $v){

	        	$batch_data[$k]['school_id'] = $v['school_id'];
                $batch_data[$k]['course_id'] = $v['course_id'];
                $batch_data[$k]['course_name'] = $v['course_name'];
                $batch_data[$k]['student_id'] = $v['student_id'];
                $batch_data[$k]['student_name'] = $v['student_name'];
                $batch_data[$k]['student_num'] = $v['student_num'];
                $batch_data[$k]['start_time'] = $v['start_time'];
                $batch_data[$k]['end_time'] = $v['end_time'];
                $batch_data[$k]['status'] = 0;
                $batch_data[$k]['create_time'] = $date_time;

	        }
	  
	        $res = Db::name('roll_call')->insertAll($batch_data);

	        if ($res) {
				$key = self::CACHE_KEY . date('Y-m-d',time());
				Cache::set($key, 1, 7*86400); //缓存7天时间
			}

		} catch (\Exception $e) {

			throw new MyException(10001, $e->getMessage());

		}

	}


}
