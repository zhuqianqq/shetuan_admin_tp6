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
Route::post('login', 'admin/user/login');
/****----------管理用户列表--------------------***/
Route::get('sysUserList', 'admin/user/getSysUserList');
/****----------管理用户详情--------------------***/
Route::get('sysUserDetails', 'admin/user/sysUserDetails');
/****----------管理用户修改-------------------***/
Route::post('sysUserUpdate', 'admin/user/sysUserUpdate');
/****----------单个管理用户修改-------------------***/
Route::post('editPwdById', 'admin/user/editPassWordById');
/****----------管理用户删除-------------------***/
Route::post('sysUserDelete', 'admin/user/sysUserDelete');
/****----------管理用户增加-------------------***/
Route::post('sysUserAdd', 'admin/user/sysUserAdd');

/****----------班级列表--------------------***/
Route::get('sysClassList', 'admin/SysClass/getSysClassList');
/****----------班级信息--------------------***/
Route::get('classInfo', 'admin/SysClass/classInfo');
/****----------班级删除-------------------***/
Route::post('classDelete', 'admin/SysClass/classDelete');
/****----------班级新增或修改-------------------***/
Route::post('classAddOrUpdate', 'admin/SysClass/classAddOrUpdate');
/****----------根据年级获取所有没有班主任的班级-------------------***/
Route::get('getClassByGrade', 'admin/SysClass/getClassByGrade');
/****----------根据年级获取所有班级-------------------***/
Route::get('getClassByGradeId', 'admin/SysClass/getClassByGradeId');
/****----------根据班级获取年级-------------------***/
Route::get('getGradeByClass', 'admin/SysClass/getGradeByClass');


/****----------课程增加或修改-------------------***/
Route::post('courseAddOrUpdate', 'admin/course/courseAddOrUpdate')->validate(\app\admin\validate\Course::class,'save');
/****----------课程列表-------------------***/
Route::get('courseList', 'admin/course/courseList');
/****----------获取所有课程信息-------------------***/
Route::get('courseInfo', 'admin/course/courseInfo');
/****----------课程上下架-------------------***/
Route::post('courseOnOrOff', 'admin/course/courseOnOrOff');
/****----------课程删除-------------------***/
Route::post('courseDelete', 'admin/course/courseDelete');


/****----------社团老师增加或修改-------------------***/
Route::post('stTeacherAddOrUpdate', 'admin/stTeacher/stTeacherAddOrUpdate')->validate(\app\admin\validate\stTeacher::class,'save');
/****----------社团老师列表-------------------***/
Route::get('stTeacherList', 'admin/stTeacher/stTeacherList');
/****----------社团老师删除-------------------***/
Route::post('stTecherDelete', 'admin/stTeacher/stTecherDelete');


/****----------班主任增加或修改-------------------***/
Route::post('teacherAddOrUpdate', 'admin/Teacher/teacherAddOrUpdate')->validate(\app\admin\validate\Teacher::class,'save');
/****----------班主任老师列表-------------------***/
Route::get('teacherList', 'admin/Teacher/teacherList');
/****----------班主任老师删除-------------------***/
Route::post('techerDelete', 'admin/Teacher/techerDelete');


/****----------学生增加或修改-------------------***/
Route::post('studentAddOrUpdate', 'admin/Student/studentAddOrUpdate')->validate(\app\admin\validate\Student::class,'save');
/****----------学生列表-------------------***/
Route::get('studentList', 'admin/Student/studentList');
/****----------学生删除-------------------***/
Route::post('studentDelete', 'admin/Student/studentDelete');



/****----------消息列表-------------------***/
Route::get('messageList', 'admin/message/messageList');
/****----------消息审核-------------------***/
Route::post('checkMessage', 'admin/message/checkMessage');