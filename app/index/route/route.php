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

/******** 班主任课程情况&缺省 *******/
Route::get('course', 'index/teacher/course');

/******** 班主任查看某个学生课程记录详情*******/
Route::get('courseByStudentDetails', 'index/teacher/courseByStudentDetails');

