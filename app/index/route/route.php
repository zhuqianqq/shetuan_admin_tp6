<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

//Route::get('think', function () {
//    return 'hello,ThinkPHP6!';
//});
/******** 登录 *******/
Route::any('login', 'index/user/loginByMinWechat');
Route::any('phoneCheck', 'index/user/loginByMinWechatPhone');

/******** 社团老师课程情况&缺省 *******/
//社团老师首页接口
Route::any('stCourse', 'index/StTeacher/course');
//社团老师排课计划
Route::any('schedule', 'index/StTeacher/schedule');
//认领社团列表
Route::any('allCourses', 'index/StTeacher/allCourses');
//认领社团
Route::any('claimCourses', 'index/StTeacher/claimCourses');
//课程情况列表
Route::any('courseInfoList', 'index/StTeacher/courseInfoList');
//课程详情接口
Route::any('shetuanDetail', 'index/StTeacher/shetuanDetail');
//点名列表接口
Route::any('callRollList', 'index/StTeacher/callRollList');
//点名操作
Route::any('dianMing', 'index/StTeacher/dianMing');

/******** 班主任课程情况&缺省 *******/
//老师首页
Route::any('course', 'index/teacher/course');

//老师课程情况列表
Route::any('courseInfo', 'index/teacher/courseInfo');

//学生报团列表
Route::any('studentInfoList', 'index/teacher/studentInfoList');

//认领班级列表
Route::any('allClasses', 'index/teacher/allClasses');

//学生报团操作
Route::any('attendCourse', 'index/teacher/attendCourse');

//认领班级操作
Route::any('claimClass', 'index/teacher/claimClass');

//具体学生的上课情况
Route::any('studentInfoDetail', 'index/teacher/studentInfoDetail');

//老师帮学生请假接口
Route::any('askForLeave', 'index/teacher/askForLeave');

//所有社团课程
Route::any('allSheTuanCourses', 'index/teacher/allSheTuanCourses');


/******** 班主任查看某个学生课程记录详情*******/
Route::get('courseByStudentDetails', 'index/teacher/courseByStudentDetails');


/******** 生成每天的点名表(定时任务)*******/
Route::any('createDianMingList', 'index/CronJob/DianMingList');


Route::any('/', 'index/user/loginByMinWechatPhone');



