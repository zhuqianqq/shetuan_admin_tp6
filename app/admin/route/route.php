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


/****----------测试--------------------***/
Route::get('testAdmin', 'admin/index/hello');
/****----------登录--------------------***/
Route::any('login', 'admin/user/login');
/****----------管理用户列表--------------------***/
Route::get('sysUserList', 'admin/user/getSysUserList');
/****----------管理用户详情--------------------***/
Route::get('sysUserDetails', 'admin/user/sysUserDetails');
/****----------管理用户修改-------------------***/
Route::post('sysUserUpdate', 'admin/user/sysUserUpdate');
/****----------管理用户修改-------------------***/
Route::post('sysUserDelete', 'admin/user/sysUserDelete');
/****----------管理用户增加-------------------***/
Route::post('sysUserAdd', 'admin/user/sysUserAdd');

/****----------班级列表--------------------***/
Route::get('sysClassList', 'admin/user/getSysClassList');
/****----------班级详情--------------------***/
Route::get('sysClassDetails', 'admin/user/sysClassDetails');
/****----------班级修改-------------------***/
Route::post('sysClassUpdate', 'admin/user/sysClassUpdate');
/****----------班级修改-------------------***/
Route::post('sysClassDelete', 'admin/user/sysClassDelete');
/****----------班级增加-------------------***/
Route::post('sysClassAdd', 'admin/user/sysClassAdd');


/****----------课程增加-------------------***/
Route::post('courseAddOrUpdate', 'admin/course/courseAddOrUpdate');
Route::get('courseList', 'admin/course/courseList');
Route::post('courseDrop', 'admin/course/courseDrop');

