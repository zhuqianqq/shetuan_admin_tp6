<?php

namespace app\index\util;

class WeixinPay
{
    const API_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    private $appId;
    private $mchId;
    private $key;
    private $notifyUrl;
    private $deviceInfo;

    public function __construct($appId, $mchId, $key, $notifyUrl = '', $deviceInfo = 'WEB')
    {
        $this->appId = $appId;
        $this->mchId = $mchId;
        $this->key = $key;
        $this->notifyUrl = $notifyUrl;
        $this->deviceInfo = $deviceInfo;
    }

    public function createNonceString()
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < 32; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function sign(array $params)
    {
        ksort($params);

        $string = "";
        foreach ($params as $key => $value) {
            if ($value != '' && !is_array($value) && $key != 'sign') {
                $string .= "$key=$value&";
            }
        }
        $string = $string . 'key=' . $this->key;

        return strtoupper(md5($string));
    }

    /**
     * 微信支付统一预下单
     * @param $data
     * @param string $tradeType
     * @param null $ip
     * @param null $openId
     * @return array
     * @throws \Exception
     */
    public function prepay($record, $tradeType = 'APP', $ip = null, $openId = null)
    {
        $ip = $ip ?: Tools::getClientIp();
        $tradeType = strtoupper($tradeType);
        $body = $record['body'] ?? '映购-购买商品';
        if (empty($body)) {
            $body = '映购-购买商品';
        }

        $params = [
            'appid' => $this->appId,
            'mch_id' => $this->mchId,
            'device_info' => $this->deviceInfo,
            'body' => $body,
            'trade_type' => $tradeType,
            'spbill_create_ip' => $ip,
            'total_fee' => intval(bcmul($record['money'], 100)),
            'out_trade_no' => $record['merOrderId'],
            'nonce_str' => $this->createNonceString(),
            'notify_url' => $this->notifyUrl,
        ];

        if ($tradeType == 'JSAPI') {
            $params['openid'] = $openId;
        }
        $params['sign'] = $this->sign($params);

        $xml = '<xml>';
        foreach ($params as $key => $value) {
            $xml .= "<$key>$value</$key>";
        }
        $xml .= '</xml>';
        $responseXml = Tools::my_curl(static::API_URL, 'post', $xml);

        // 处理数据
        $result = $this->xmlToArray($responseXml);

        if (empty($result)) {
            throw new \Exception('请求网关超时', 1001);
        }

        if ($result['return_code'] != 'SUCCESS') {
            throw new \Exception('支付下单失败-预支付错误: ' . $result['return_msg'], 1002);
        }

        if ($result['result_code'] != 'SUCCESS') {
            throw new \Exception('支付下单失败-微信平台错误: ' . $result['err_code_des'], 1003);
        }

        if ($result['sign'] != $this->sign($result)) {
            throw new \Exception('支付下单失败-响应签名错误', 1004);
        }

        return $result;
    }

    /**
     * 生成微信小程序支付js参数
     *
     * @see https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455784134
     * @param $openId
     * @param $ip
     * @return array
     */
    public function getXcxJsApiParams($record, $openId, $ip)
    {
        $wxOrder = $this->prepay($record, 'JSAPI', $ip, $openId);

        //生成页面调用参数
        $params = [
            'appId' => $this->appId,
            'timeStamp' => time() . "",
            'nonceStr' => $wxOrder['nonce_str'],
            'package' => "prepay_id=" . $wxOrder['prepay_id'],
            'signType' => 'MD5',
        ];

        $params["paySign"] = $this->sign($params);

        // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
        $params['timestamp'] = $params['timeStamp'];
        unset($params['timeStamp']);

        return $params;
    }

    private function xmlToArray($xml)
    {
        $xmlObj = simplexml_load_string($xml);
        $data = [];
        foreach ($xmlObj as $k => $v) {
            if (is_object($v)) {
                $data[$k] = $v->__toString();
            } else {
                $data[$k] = $v;
            }
        }

        return $data;
    }
}
