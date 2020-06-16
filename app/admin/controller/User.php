<?php

namespace app\admin\controller;

use app\admin\BaseController;
use app\admin\MyException;
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
     * 退出登录
     */
    public function loginOut(Request $request)
    {
        $user_id = $request->user_id;
        if ($user_id) {
            SysUserService::forgetAccessKey($this->user_id);
        }
        return json_ok();
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
            return json_error(10001);
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
            return json_error(10001);
        }
        return SysUserService::sysUserDelete($sysUserId);
    }

    /**
     * 管理用户修改
     * @param  Request $request
     * @return json
     */
    public function editPassWordById(Request $request)
    {
        //it$data['user_id'] = $this->request->st_user['user_id'];
        $data['user_id'] = 1;
        $data['password'] = input('password', '', 'string');
        if (empty($data['user_id']) || empty($data['password'])) {
            return json_error(10002);
        }

        $res = SysUserService::updatePassword($data);
        return json_ok($res);
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
        $param['account'] = $request->param('account'); //登录账户
        $param['userName'] = $request->param('userName');//真实姓名
        $param['enable'] = $request->param('enable', 1);//是否启用(默认启用)
        $param['password'] = $request->param('password');//密码
        $param['mobile'] = $request->param('mobile');//手机
        $param['userType'] = $request->param('userType');//角色

        if (empty($param['sysUserId'])) {
            return json_error(10002);
        }
        return json_ok(SysUserService::sysUserUpdate($param));
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
        $param['userType'] = $request->param('userType');//角色
        if (empty($param['account']) || empty($param['userName']) || empty($param['password']) || empty($param['userType'])) {
            return json_error(10001);
        }
        $res = SysUserService::sysUserAdd($param);

        return json_ok($res);
    }
}
