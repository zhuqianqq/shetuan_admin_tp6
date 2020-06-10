<?php
/**
 */

namespace app\index\util;

use think\facade\Cache;

class SmsHelper
{
    /**
     * @var string key前缀
     */
    public static $prefix = "dani:smscode:";

    /**
     * 获取缓存key
     * @param int $phone
     * @param string $type
     * @return string
     */
    public static function getCacheKey($phone, $type = "")
    {
        $key = self::$prefix . "{$phone}";
        if (!empty($type)) {
            $key .= ":" . $type;
        }
        return $key;
    }

    /**
     * 发送短信验证码
     * @param string $phone
     * @param string $msg
     * @return array
     */
    public static function sendSmsMessage($phone, $msg)
    {
        $param = [
            'account' => 'N7370168',
            'password' => 'h7EKyenI7',
            'phone' => trim($phone),
            'msg' => $msg,
            'report' => 'true',
        ];
        $res = Tools::curlPost('http://smssh1.253.com/msg/send/json', json_encode($param, JSON_UNESCAPED_UNICODE));

        if (isset($res['code']) && $res['code'] == '0') {
            return ['code' => 0, 'msg' => '发送成功'];
        }
        $err_msg = $res['errorMsg'] ?? '发送失败';

        return ['code' => 500, 'msg' => $err_msg];
    }

    /**
     * @param $phone
     * @param $vcode
     * @param string $type 类型
     * @return bool
     */
    public static function checkVcode($phone, $vcode, $type = "")
    {
        $cache_key = self::getCacheKey($phone, $type);
        $cache_vcode = Cache::get($cache_key);

        return $cache_vcode == $vcode;
    }

    /**
     * 清除cache
     * @param string $phone
     * @param string $type
     */
    public static function clearCacheKey($phone, $type = "")
    {
        $cache_key = self::getCacheKey($phone, $type);
        Cache::del($cache_key);
    }

    /**
     * 清除cache
     * @param string $userId
     */
    public static function clearLoginCacheKey($userId)
    {
        $cache_key = config('cachekeys.acc_key') . $userId;
        Cache::del($cache_key);
    }
}
