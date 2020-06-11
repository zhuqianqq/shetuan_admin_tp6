<?php
namespace app\index\util;

use think\facade\Cache;

class WechatHelper
{
    public static function getWechatLoginInfo($code, $iv, $encryptedData)
    {
        Tools::addLog("wechat", "iv", $iv);
        Tools::addLog("wechat", "encryptedData", $encryptedData);
        $wechat_config = config('weixin');
        $realUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $wechat_config['appid'] . '&secret=' . $wechat_config['secret'] . '&js_code=' . $code . '&grant_type=authorization_code';
        $res = Tools::curlGet($realUrl, null);
        Tools::addLog("wechat", "取得微信授权结果", json_encode($res));
        if ($res == null || !isset($res["session_key"])) {
            return null;
        }
        $session_key = $res["session_key"];
        $pc = new WXBizDataCrypt ($wechat_config['appid'], $session_key);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        Tools::addLog("wechat", "取得微信授权结果decryptData", $data);
        Tools::addLog("wechat", "errCode", $errCode);
        if ($errCode == 0) {
            return $data;
        } else {
            return null;
        }
    }

    public static function getAccessToken()
    {
        $weiXin_config = config('weixin');
        $app_id = $weiXin_config["appid"];
        $secret = $weiXin_config["secret"];

        $key = 'WechatTokenKey';
        $cache = Cache::store('redis')->get($key);
        if (!empty($cache)) {
            return $cache;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $app_id . "&secret=" . $secret . "";
        $res = Tools::curlGet($url, null);
        if ($res == null || !isset($res["access_token"])) {
            return "";
        }
        Cache::store('redis')->set($key, $res['access_token'], $res['expires_in'] - 60);
        return $res["access_token"];
    }

    public static function getOpenidByCode($code)
    {
        $wechat_config = config('weixin');;
        $realUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $wechat_config['appid'] . '&secret=' . $wechat_config['secret'] . '&js_code=' . $code . '&grant_type=authorization_code';
        $res = Tools::curlGet($realUrl, null);
        return $res;
    }

    public static function getMiniQr($page, $scene, $width, $access_token)
    {
        $data = array("path" => $page, "width" => $width);
        if (!empty($scene)) {
            $data = array("path" => $page, "width" => $width, "scene" => $scene);
        }
        $header = array();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
        $res = Tools::curlPost($url, $data, true, $header, false);
        return $res;
    }

    public static function sendMsg($data, $access_token)
    {
        $header = array();
        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=" . $access_token;
        $res = Tools::curlPost($url, $data, true, $header, false);
        return $res;
    }
}
