<?php
namespace app\index\controller;

use app\index\BaseController;
use EasyWeChat\Factory;
use think\Request;

class Index extends BaseController
{
    
    public function login(Request $request )
    {
        $code = $request->param('code');
        $config = [
            'app_id' => 'wx87e42b5e0c69bac2',
            'secret' => '671df6b64213a1cf395f24daa4025de2',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__.'/wechat.log',
            ],
        ];

        $app = Factory::miniProgram($config);
        $res = $app->auth->session($code);
        return json_ok($res,200);

    }
}
