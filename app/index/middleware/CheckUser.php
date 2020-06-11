<?php
declare (strict_types=1);

namespace app\index\middleware;

use app\index\util\JwtUtil;
use think\facade\Cache;
use think\facade\Config;
use app\index\MyException;
use app\index\service\UserService;

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

            throw new MyException(11101);
        }
        $notCheckUrl = [

            // 登录
            'login',
            // 登录
            'checkPhone',
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
            throw new MyException(11101);
        }
         // if($userToken != 'abc123456'){
         //     throw new MyException(11101);
         // }


        $userToken = think_decrypt($userToken);
        $payload = JwtUtil::decode($userToken);
        if ($payload === false || empty($payload->user_id) || empty($payload->login_time)) {
           throw new MyException(11101);
        }

        $cacheKey = config('cachekeys.acc_key') . $payload->user_id;
        $isLogout = Cache::get($cacheKey);
        if (!$isLogout) {
           throw new MyException(11102);
        }

        // 实时用户数据
        $user = UserService::getInfoById($payload->user_id);

        //用户是否存在
        if (empty($user)) {
           throw new MyException(11104);
        }

        $user['phone'] = $payload->phone;
        $request->user = $user;
        return $next($request);
    }
}
