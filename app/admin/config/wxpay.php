<?php
/**
 * 微信支付配置
 */
return [
    'app' => [
        'mch_id' => '1596127631',// 商户号
        'app_id' => 'wx4cf8dc1048b790c2', // 应用appid
        'app_secret' => '992e85df19031bfedaaebe72d2555aba',    // 商户的私钥
        'key' => 'HiT390SjfYgLUoYxaeYbVrMdR44eVSO2', // 秘钥
        'notify_url' => env("APP_URL").'/api/weixinpays/notify/channel/app',
    ],
    'xcx' => [
        'mch_id' => '1596127631',// 商户号
        'app_id' => 'wxd9e0eeff330bf425', // 应用appid
        'app_secret' => 'cb4e2f65b6ee3d2ab6cebd4e762c3fe9',    // 商户的私钥
        'key' => 'HiT390SjfYgLUoYxaeYbVrMdR44eVSO2', // 秘钥
        'notify_url' => env("APP_URL").'/api/weixinpays/notify/channel/xcx',
        'web_notify_url' => env("APP_URL").'/api/weixinpays/webNotify/channel/xcx',
    ]
];
