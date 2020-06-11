<?php
declare (strict_types = 1);

namespace app\index;

use think\App;
use think\exception\ValidateException;
use think\Validate;
use app\admin\util\JwtUtil;
use think\facade\Cache;
use think\facade\Config as sysConfig;
use app\index\MyException;
use app\index\service\UserService;



/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {

        $request_uri = $this->request->request()['s'] ?? '';
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
        if ($isCheck) {

            // JWT用户令牌认证，令牌内容获取
            $userToken = $this->request->header('x-access-token');

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
            $this->request->st_user = $user;
        }

    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

}
