<?php
declare (strict_types=1);

namespace app\admin\middleware;

use app\admin\util\JwtUtil;
use think\facade\Cache;
use think\facade\Config;
//use app\service\UserService;

/**
 * 身份验证
 * Class CheckUser
 * @package app\middleware
 */
class CheckUser
{
    public function handle($request, \Closure $next)
    {

        $request_uri = $request->request()['s'] ?? '';
        if (!$request_uri) {

            throw new \app\admin\MyException(11101);
        }
        $notCheckUrl = [

            // 登录
            'login',
            // 发送验证码
            'sendCode',
            // 上传图片
            'upload',
            // 初始化
            'init',
            // 版本检测
            'checkV',
        ];

        $isCheck = true;
        foreach ($notCheckUrl as $v) {
            if (stripos($request_uri, $v) !== false) {
                $isCheck = false;
            }
        }
        if (!$isCheck) {
            return $next($request);
        }


        // JWT用户令牌认证，令牌内容获取
        $userToken = $request->header('x-access-token');

        if (empty($userToken)) {
            throw new \app\admin\MyException(11101);
        }
         if($userToken != 'abc123456'){
             throw new \app\admin\MyException(11101);
         }


//        $userToken = think_decrypt($userToken);
//        $payload = JwtUtil::decode($userToken);
//        if ($payload === false || empty($payload->user_id) || empty($payload->login_time)) {
//            throw new \app\admin\MyException(11101);
//        }
//        $cacheKey = config('cachekeys.acc_key') . $payload->user_id;
//        $isLogout = Cache::get($cacheKey);
//        if ($isLogout) {
//            throw new \app\admin\MyException(11102);
//        }
//        //用户登录有效期
//        $userLoginTime = Config::get('system.user_login_time');
//        if ($payload->login_time < time() - $userLoginTime) {
//            throw new \app\admin\MyException(11102);
//        }
//        $logout = $payload->logout ?? false;
//        if ($logout) {
//            throw new \app\admin\MyException(11102);
//        }
//        // todo redis
//        // 实时用户数据
//        $user = \app\serviceapp\UserService::getInfoById($payload->user_id);
//
//        //用户是否存在
//        if (empty($user)) {
//            throw new \app\admin\MyException(11104);
//        }
//
//        //是否多设备登录
//        if (!empty($user['last_login_time']) && strtotime($user['last_login_time']) != $payload->login_time) {
//            throw new \app\admin\MyException(11103);
//        }
//
//        $user['phone'] = $payload->phone;
//        $request->dani_user = $user;
        return $next($request);
    }
}
