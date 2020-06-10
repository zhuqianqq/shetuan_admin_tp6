<?php
declare (strict_types=1);

namespace app\index\service;


use app\common\model\User;
use app\common\model\Teacher;
use app\common\model\TuanTeacher;

use app\index\MyException;
use app\index\util\JwtUtil;
use app\index\util\SmsHelper;
use app\index\util\Tools;
use app\index\util\WechatHelper;

use think\facade\Cache;
use think\facade\Db;

/**
 * 用户
 * Class UserService
 * @package app\service
 */
class UserService
{

    /**
     * @param $userId
     * @return array|null|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getUserById($userId)
    {
        if (!$userId) throw new MyException(10002);
        $oneUser = User::where('user_id = :user_id', ['user_id' => $userId])->find();
        return $oneUser;
    }

    /**
     * 获取用户信息
     */
    public static function getUserInfo($userId)
    {
        $table_user = User::$_table;
        $table_user_detail = UserDetails::$_table;
        $oneUser = Db::name($table_user)
            ->alias('u')
            ->field("u.nick_name nickName,u.status, u.head_image headImage, u.sex, u.autograph, u.birthday, u.data_page dataPage, u.sida_num sidaNum, u.last_login_time lastLoginTime,ud.fuse,ud.tixing, ud.xijie, ud.fengge, ud.shengao, ud.tizhong,ud.nianling,ud.zhiye, ud.zhiwei, ud.aihao")
            ->join("$table_user_detail ud", "u.user_id = ud.user_id", 'left')
            ->where(['u.user_id' => $userId])->find();
        if (!$oneUser) {
            throw new MyException(10004);
        }
        
        return $data;
    }


    /**
     * 小程序绑定手机号这一步才是真正的登录
     */
    public function loginByMinWechatPhone()
    {
        Db::startTrans();
        try {
            $userid = $this->request->post("user_id", '', "trim");
//            $phone = $this->request->post("phone", '', "trim");
            $code = $this->request->post("code", '', "trim");
            $iv = $this->request->post("iv", '');
            $encryptedData = $this->request->post("encryptedData", '');

            if (empty($iv) || empty($encryptedData) || empty($code)) {
                throw new \Exception("参数错误", 100);
            }
            // 判断是否已经绑定了手机号
            $exist_user = Users::get($userid);
            if(!empty($exist_user) && !empty($exist_user->userPhone)) {
                // 判断手机号是否一致 如果不一致则直接返回
                $data = Member::where('user_id', $userid)->find();
                $data['access_key'] = $exist_user->access_key;
                $data['userPhone'] = $exist_user->userPhone;
                Db::commit();
                return $this->outJson(0, "登录成功！",$data);
            }
            
            $loginInfo = WechatHelper::getWechatLoginInfo($code, $iv, $encryptedData); //以code换取openid
            if (empty($loginInfo)) {
                throw new \Exception("获取信息失败" . json_encode($loginInfo), 100);
            }
            $loginInfo = json_decode($loginInfo, true);
            $phone = $loginInfo['phoneNumber'];

            if (ValidateHelper::isMobile($phone) == false || !$userid) {
                throw new \Exception("参数错误", 100);
            }

            $exist_user= Users::where('userPhone = ' . $phone . " and plat = 1")->find();
            if($exist_user != null) {
                return $this->outJson(100, "此手机号已绑定其它账号！");
            }

            Users::where([
                "userId" => $userid,
            ])->update([
                'userPhone' => $phone,
            ]);

            $data = Member::where('user_id', $userid)->find();
            Member::setOtherInfo($data);
            $data['userPhone'] = $phone;
            Users::where([
                "userId" => $userid,
            ])->update([
                'access_key' => $data['access_key']
            ]);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $this->outJson(0, "登录失败", $e->getMessage() ?? '接口异常');
        }

        return $this->outJson(0, "登录成功", $data);
    }


    /**
     * 小程序登录
     * @param $data
     * @return mixed
     */
    public static function loginByMinWechat($data)
    {
        $loginInfo = WechatHelper::getWechatLoginInfo($data['code'], $data['iv'], $data['encryptedData']); // 以code换取openid
        if (empty($loginInfo)) {
            throw new MyException(12006);
        }
        $loginInfo = json_decode($loginInfo, true);
        $unionId = isset($loginInfo['unionId']) ? $loginInfo['unionId'] : '';
        $openId = isset($loginInfo['openId']) ? $loginInfo['openId'] : '';

        if (empty($loginInfo)) {
            throw new MyException(12006);
        }
        if (empty($openId)) {
            throw new MyException(12013);
        }

        $data['openid'] = $openId;
        $data['unionid'] = $unionId;
        //$data['openIdType'] = 1; // 0 APP 1 小程序 2 web
        return static::loginByWechat($data);
    }

    /**
     * APP微信登录
     * @param $data
     * @return mixed
     */
    public static function loginByWechat($data)
    {
        $unionid = $data['unionid'];
        $openid = $data['openid'];
       
        if (empty($unionid)) throw new MyException(12013);

        $findByPhone = User::where(['unionid' => $unionid])->find();

        if (empty($findByPhone)) {
            // 没有数据，则进行注册
            $userid = static::register($data); // 注册用户
        } 

        $genToken = [];
        $genToken['user_id'] = $userid;
        $genToken['phone'] = '';
        $userToken = static::genToken($genToken);

        $returnData['userId'] = $userid;
        $returnData['accessKey'] = $userToken;
        return $returnData;
    }

    /**
     * 注册
     * @param array $data
     */
    public static function register($data)
    {
        $insert_data = [
            "unionid" => $data["unionid"], 
            "open_id" => $data["openid"], 
            "user_role" => $data["user_role"], 
            'register_time' => date("Y-m-d H:i:s"),
        ];
        $userid = User::insertGetId($insert_data);
        //用户角色  1-学校老师  2-社团老师  3-家长
        if($data["user_role"] == 1){
            //TuanTeacher::insert()
        }else if($data["user_role"] == 2){
            //Teacher::insert()
        }

        return $userid;
    }

    
    /**
     * 查找
     * @param $userid
     */
    public static function getInfoById($userid)
    {
        $userTable = User::$_table;
        return Db::name($userTable)->where(['user_id' => $userid])->find();
    }


    /**
     * 生成token
     * @param $data
     * @return string
     */
    public static function genToken($data)
    {
        // 数据处理和令牌获取
        $time = time();

        // 令牌生成
        $payload['user_id'] = $data['user_id'];
        $payload['phone'] = $data['phone'];
        $payload['login_time'] = $time;
        $user_token = think_encrypt(JwtUtil::encode($payload));
        return $user_token;
    }
}
