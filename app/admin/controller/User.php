<?php

namespace app\admin\controller;

use app\admin\BaseController;
use think\Request;
use app\admin\service\SysUserService;

class User extends BaseController
{
    /**
     * 登录
     * @param  Request $request
     * @return json
     */
    public function login(Request $request)
    {
        //接收数据
        $data = [
            'account' => $request->param('account', 'trim'),
            'password' => $request->param('password', 'trim'),
        ];

        if (empty($data['account'])) {
            return json_error(12007);
        }
        if (empty($data['password'])) {
            return json_error(12008);
        }
        // 登录验证并获取包含访问令牌的用户
        $result = SysUserService::login($data['account'], $data['password']);
        return json_ok($result, 200);

    }

    /**
     * 管理用户列表
     * @param  Request $request
     * @return json
     */
    public function getSysUserList(Request $request)
    {
        $param = [];
        $param['page'] = $request->param('page', 1);
        $param['pageSize'] = $request->param('pageSize', 10);
        $param['condition'] = $request->param('condition');
        return SysUserService::getSysUserList($param);
    }

    /**
     * 管理用户详情
     * @param  Request $request
     * @return json
     */
    public function sysUserDetails(Request $request)
    {
        $sysUserId = $request->param('sysUserId'); //管理用户ID
        if (empty($sysUserId)) {
            return json_error(100, '请传入管理用户ID');
        }
        return SysUserService::sysUserDetails($sysUserId);
    }

    /**
     * 管理用户删除
     * @param  Request $request
     * @return json
     */
    public function sysUserDelete(Request $request)
    {
        $sysUserId = $request->param('sysUserId'); //管理用户ID
        if (empty($sysUserId)) {
            return json_error(100, '请传入管理用户ID');
        }
        return SysUserService::sysUserDelete($sysUserId);
    }
    /**
     * 管理用户修改
     * @param  Request $request
     * @return json
     */
    public function sysUserUpdate(Request $request)
    {
        $param = [];
       // $param['nowUserId'] = $request->param('nowUserId'); //系统登录ID
        $param['sysUserId'] = $request->param('sysUserId'); //账户ID
     //   $param['account'] = $request->param('account'); //登录账户
        $param['userName'] = $request->param('userName');//真实姓名
        $param['enable'] = $request->param('enable', 1);//是否启用(默认启用)
        $param['password'] = $request->param('password');//密码
//        if(empty( $param['nowUserId'])){
//            return json_error(100, '请传入系统当前登录的用户ID');
//        }
        if (empty($param['sysUserId'])) {
            return json_error(100, '请传入账户ID');
        }
        return SysUserService::sysUserUpdate($param);
    }

    /**
     * 管理用户增加
     * @param  Request $request
     * @return json
     */
    public function sysUserAdd(Request $request)
    {
        $param = [];
        $param['account'] = $request->param('account'); //登录账户
        $param['userName'] = $request->param('userName');//真实姓名
        $param['enable'] = $request->param('enable', 1);//是否启用(默认启用)
        $param['password'] = $request->param('password');//密码
        $param['mobile'] = $request->param('mobile');//密码
        if(empty($param['account']) || empty($param['userName']) || empty($param['enable']) ||empty($param['password'])){
            return json_error(100, '缺少参数');
        }
        return SysUserService::sysUserAdd($param);
    }
}
