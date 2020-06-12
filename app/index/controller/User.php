<?php
namespace app\index\controller;

use app\index\BaseController;
use app\index\service\UserService;
use think\facade\Cache;
use think\facade\Env;
use app\index\MyException;


class User extends BaseController
{
    
    /**
     * 小程序授权登录
     * @return array
     */
    public function loginByMinWechat()
    {

        //try {
            $code = $this->request->param("code", '', "trim");
            $iv = $this->request->param("iv", '', "trim");
            $encryptedData = $this->request->param("encryptedData", '', "trim");
            $user_role = $this->request->param("userRole", '');
            $phone = $this->request->param("phone", '');
            $avatar = $this->request->param("avatar", '');
            
            if (empty($code) || empty($iv) || empty($encryptedData) || empty($user_role) || empty($phone)) {
                throw new MyException(10002);
            }
            $data['code'] = $code;
            $data['iv'] = $iv;
            $data['encryptedData'] = $encryptedData;
            $data['user_role'] = $user_role;
            $data['phone'] = $phone;
            $data['avatar'] = $avatar;
            $userData = UserService::loginByMinWechat($data);
            //SmsHelper::clearLoginCacheKey($userData['userId']);
            //unset($userData['userId']);
      
        // } catch (\Exception $ex) {
        
        //     throw new MyException(10005, "接口异常:" . $ex->getMessage());
        // }

        return json_ok($userData, 0);
    }


    /**
     * 小程序电话授权登录
     * @return array
     */
    public function loginByMinWechatPhone()
    {
            $code = $this->request->param("code", '', "trim");
            $iv = $this->request->param("iv", '', "trim");
            $encryptedData = $this->request->param("encryptedData", '', "trim");
            $user_role = $this->request->param("userRole", '');
            return json_ok($this->request->param(), 0);
            if (empty($code) || empty($iv) || empty($encryptedData) || empty($user_role)) {
                throw new MyException(10002);
            }
            $data['code'] = $code;
            $data['iv'] = $iv;
            $data['encryptedData'] = $encryptedData;
            $data['user_role'] = $user_role;
            $userData = UserService::loginByMinWechatPhone($data);
            return json_ok($userData, 0);
    }

    /**
     * 用户详情
     */
    public function userDetail()
    {
        // todo redis
        $user = $this->request->dani_user;
        if (empty($user)) {
            throw new \app\MyException(10004);
        }
        $userId = $user['user_id'];
        if (empty($userId)) {
            throw new \app\MyException(10004);
        }
        $userInfo = UserService::getUserInfo($userId);
        return json_ok($userInfo, 0);
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        $user = $this->request->dani_user;
        if (empty($user)) {
            throw new \app\MyException(10004);
        }
        $cacheKey = config('cachekeys.acc_key') . $user['user_id'];
        Cache::set($cacheKey, 1);
        return json_ok((object)[], 0);
    }
}
