<?php

namespace app\index\controller;

use app\index\MyException;
use app\common\model\Course;


/**
 * 生成每天的点名表
 * Class Index
 */

class CronJob extends Base {

	//const MES_KEY = 'MessageKey:';

	public static function rollCall() {
		try {

			$weekday = date("w", time());
	        $weekday==0 && $weekday=7;//如果是0 改为7

			 //今天的社团班级信息
			$todayCourses = Course::where('status',1)
						 ->where('weeks','like','%'.$weekday.'%')
						 ->select()->order('id','asc')->toArray();

	        if(!$todayCourses){
	            return [];
	        }

	        
	        foreach ($todayCourses as $key => $value) {
	        	# code...
	        }




		} catch (\Exception $e) {

			throw new MyException(10001, $e->getMessage());

		}

	}


}
