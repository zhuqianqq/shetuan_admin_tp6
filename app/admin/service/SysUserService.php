<?php
declare (strict_types=1);

namespace app\admin\service;


use app\admin\MyException;
use app\admin\util\JwtUtil;
use app\common\model\SysUser;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use app\admin\model\BaseModel;

/**
 * 管理员
 * Class SysUserService
 * @package app\service
 */
class SysUserService
{

    /**
     * 登录
     * @param string $account 用户名
     * @param string $password 未加密的密码
     * @return array 对象数组，包含字段：userToken，已编码的用户访问令牌；user，用户信息。
     * @throws \app\MyException
     */
    public static function login($account, $password)
    {
        // 查找身份，验证身份
        $user = Db::table('sys_user')->where('account',$account)->find();
        if (empty($user)) {
            throw new MyException(11104);
        }

        //密码验证
        if ($user['password'] !== encrypt_pass($password)) {
            throw new MyException(11105);
        }

        $user['login_time'] = date('Y-m-d H:i:s',time());
        // 令牌生成
        $user_token            = think_encrypt(JwtUtil::encode($user));
        $userLoginTime = Config::get('system.user_login_time');
        Cache::set('ACCESS_TOKEN:'.$user['user_id'], $user_token, $userLoginTime);

        // 数据处理和令牌获取

        return array('user_token' => $user_token, 'account' => $user['account'], 'userName' => $user['user_name'], 'id' =>$user['user_id']);
    }

    /**
     * 从缓存中删除用户的access_key
     * @param int $user_id
     * @param string $from
     * @return bool
     */
    public static function forgetAccessKey($user_id)
    {
        $authKey = 'ACCESS_TOKEN:'. $user_id;
        return Cache::delete($authKey);
    }

    /**
     * 管理员列表
     * @param array $param 参数数组
     * @return json
     */
    public static function getSysUserList($param)
    {

        $where = 'enable=1 ';
        $model = Db::table('sys_user')->alias('sy');
        if (!empty($param['condition'])) {
            $where .= ' AND sy.account like "%' . $param['condition'] . '%" or sy.user_name like "%' . $param['condition'] . '%" or sy.mobile like "%' . $param['condition'] . '%"';
            $model->where($where);
        }

        $res = $model->field('sy.user_id as userId,sy.account,sy.user_name as userName,sy.mobile,sy.user_type as userType,sy.school_id as schoolId,sy.create_time as createTime,user_type userType,enable')
            ->where($where)->paginate(['page' => $param['page'], 'list_rows' => $param['pageSize']])->toArray();
        if (empty($res)) {
            return json_ok((object)array());
        }
        $list = ['total' => $res['total'], 'currentPage' => $res['current_page'], 'lastPage' => $res['last_page'], 'data' => $res['data']];
        return json_ok($list);
    }

    /**
     * 通过管理员ID获取详情
     * @param string $userId 管理员ID
     * @return json
     */
    public static function getInfoById($userId)
    {
        $res = Db::table('sys_user')->where('user_id',$userId)->find();
        return $res;

    }
    /**
     * 管理员详情
     * @param string $sysUserId 管理员ID
     * @return json
     */
    public static function sysUserDetails($sysUserId)
    {
        $res = SysUser::alias('sy')->field('sy.user_id as userId,sy.account,sy.user_name as userName,sy.mobile,sy.user_type as userType,sy.school_id as schoolId,sy.enable,sy.create_time as createTime,password pwd')->where('user_id',$sysUserId)->find();
        if (empty($res)) {
            return json_ok((object)array(), 0);
        }
        $res['pwd'] = think_decrypt($res['pwd']);
        return json_ok($res);
    }

    /**
     * 管理员删除
     * @param string $sysUserId 管理员ID
     * @return json
     */
    public static function sysUserDelete($sysUserId)
    {

        BaseModel::beginTrans();
        try {
            if (strpos($sysUserId, ',') !== false) {

                SysUser::where('user_id', 'in', $sysUserId)->update(['enable' => '2']);
            } else {
                SysUser::where('user_id', $sysUserId)->save(['enable' => '2']);
            }
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(10001, $e->getMessage());
        }
        BaseModel::commitTrans();
        return json_ok((object)array());
    }

    /**
     * 修改密码
     * @param $param
     * @return \json
     */
    public static function updatePassword($param)
    {
        $isSysUserId = SysUser::alias('sy')->where('user_id', $param['user_id'])->find();
        if (empty($isSysUserId)) {
            return json_error(10001);
        }
        try {
            $isSysUserId->password = encrypt_pass($param['password']);
            $isSysUserId->save();
        } catch (\Exception $e) {
            return json_error(10001, $e->getMessage());
        }

        return (object)array();
    }

    /**
     * 管理员编辑
     * @param array $param 参数数组
     * @return json
     */
    public static function sysUserUpdate($param)
    {

        $isSysUserId = SysUser::alias('sy')->where('user_id', $param['sysUserId'])->find();
        if (empty($isSysUserId)) {
            return json_error(10001, '记录不存在');
        }
        /*if($param['nowUserId'] != 1){
            if($param['sysUserId'] == $param['nowUserId']){
                if($isSysUserId['enable'] !=$param['enable']){
                    return json_error(10001, '普通账号无权对自身封禁状态做更改');
                }
            }
            if($param['sysUserId'] ==1){
                return json_error(10001, '无权对超级管理员进行操作');
            }
        }
        if($isSysUserId['account'] != $param['account']){
            return json_error(10001, '登录账号无法修改，请重新提交');
        }*/

        BaseModel::beginTrans();
        try {
            $data = [];
            $data['user_name'] = $param['userName'];
            $data['enable'] = $param['enable'];
            $data['account'] = $param['account'];
            $data['mobile'] = $param['mobile'];
            $data['user_type'] = $param['userType'];
            if (!empty($param['password'])) {
                $data['password'] = encrypt_pass($param['password']);
            }
            SysUser::where('user_id', $param['sysUserId'])->data($data)->update();
    } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            return json_error(10001, $e->getMessage());
        }
        BaseModel::commitTrans();
        return json_ok((object)array(), 200);


    }

    /**
     * 管理员新增
     * @param array $param 参数数组
     * @return json
     */
    public static function sysUserAdd($param)
    {

        $isAccount = SysUser::alias('sy')->where('account', $param['account'])->find();
        if ($isAccount && $isAccount['enable'] == 1) {
            return json_error(11109);
        }
        BaseModel::beginTrans();
        try {
            $data = [];
            $data['account'] = $param['account'];
            $data['user_name'] = $param['userName'];
            $data['password'] = $param['password'] ? encrypt_pass($param['password']) : encrypt_pass('123456');
            $data['mobile'] = $param['mobile'];
            $data['user_type'] = $param['userType'];
            $oneUser = new SysUser();
            $oneUser->save($data);
        } catch (\Exception $e) {
            BaseModel::rollbackTrans();
            throw new MyException(10001, $e->getMessage());
        }
        BaseModel::commitTrans();
        return (object)array();
    }

}
