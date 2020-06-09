<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use app\util\Tools;

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int $expire 过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? config('system.pass_salt') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }

    $str = str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
    return strtoupper(md5($str)) . $str;
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? config('system.pass_salt') : $key);
    $data = substr($data, 32);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 请求正确返回
 * @param string $msg
 * @param array $data
 * @return json
 */
function json_ok($data = [], $code = 10000, $msg = '')
{
    $result['status'] = 1;
    $result['data'] = $data;
    $result['message'] = isset(config('error')[$code]) ? config('error')[$code] : '';
    $result['code'] = $code;
    return json($result);
}

/**
 * 请求错误返回
 * @param string $code
 * @param string $msg
 * @return json
 */
function json_error($code = 10001, $msg = '')
{
    if ($msg == '') {
        $result['message'] = isset(config('error')[$code]) ? config('error')[$code] : '';
    } else {
        $result['message'] = $msg;
    }
    $result['status'] = 0;
    $result['code'] = $code;
    return json($result);
}


/**
 * 请求正确返回 —— layui表格渲染格式数据返回
 * @param string $msg
 * @param array $data
 * @return json
 */
function json_form_ok($data = [], $count, $code = 0, $msg = 'success')
{
    $result['data'] = $data;
    $result['count'] = $count;
    $result['msg'] = isset(config('error')[$code]) ? config('error')[$code] : '';
    $result['code'] = $code;
    return json($result);
}


/**
 * 用户密码加密方法，可以考虑盐值包含时间（例如注册时间），
 * @param string $pass 原始密码
 * @return string 多重加密后的32位小写MD5码
 */
function encrypt_pass($pass)
{
    if ('' == $pass) {
        return '';
    }
    $salt = config('app.pass_salt');
    return md5(sha1($pass) . $salt);
}

/**
 * 数据 类型转换
 * @access protected
 * @param  mixed $value 值
 * @param  string|array $type 要转换的类型
 * @return mixed
 */
function transform($value, $type)
{
    if (is_null($value)) {
        return;
    }

    if (is_array($type)) {
        [$type, $param] = $type;
    } elseif (strpos($type, ':')) {
        [$type, $param] = explode(':', $type, 2);
    }

    switch ($type) {
        case 'string':
            $value = (string)$value;
            break;
        case 'integer':
            $value = (int)$value;
            break;
        case 'float':
            if (empty($param)) {
                $value = (float)$value;
            } else {
                $value = (float)number_format($value, (int)$param, '.', '');
            }
            break;
        case 'boolean':
            $value = (bool)$value;
            break;
        case 'timestamp':
            if (!is_numeric($value)) {
                $value = strtotime($value);
            }
            break;
        case 'datetime':
            $value = is_numeric($value) ? $value : strtotime($value);
            if (empty($param)) {
                $value = date('Y-m-d H:i:s', $value);
            } else {
                $value = date($param, $value);
            }
            break;
        case 'object':
            if (is_object($value)) {
                $value = json_encode($value, JSON_FORCE_OBJECT);
            }
            break;
        case 'array':
            $value = (array)$value;
        case 'json':
            $option = !empty($param) ? (int)$param : JSON_UNESCAPED_UNICODE;
            $value = json_encode($value, $option);
            break;
        case 'serialize':
            $value = serialize($value);
            break;
        default:
            break;
    }

    return $value;
}

/**
 * redis lock
 * @param $cacheKey
 * @param int $time
 * @return bool
 */
function commitCheck($cacheKey, $time = 1)
{
    $redis = \think\facade\Cache::handler();
    $isLock = false;
    if ($redis->setnx($cacheKey, $time)) {
        $redis->expire($cacheKey, $time);
        $isLock = true;
    }
    return $isLock;
}

/**
 * 生成订单号
 */
function generateOrderNo()
{
    //  redis key 当前年月日加4位订单序号，如202003270001 超过四位则继续
    $redisKey = "s:ordernum:date:" . date('Ymd');
    $num = \think\facade\Cache::inc($redisKey);// redis读取，替换一下
    if (!$num) {
        $sn = date('Ymd') . '0001';
    } else {
        $sn = date('Ymd') . sprintf("%04d", $num);
    }
    return $sn;
}

/**
 * 返回图片保存地址
 * @return string
 */
function getSavePath($file_name = '', $type = 'jpg')
{
    $save_path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR . date("Ymd") . DIRECTORY_SEPARATOR; //保存目录
    if (!file_exists($save_path)) {
        @mkdir($save_path, 0755, true); //创建目录
    }

    if (!empty($file_name) && file_exists($file_name)) {
        $info = @getimagesize($file_name);
        if ($info) {
            $img_type = image_type_to_extension($info[2], true); //图片类型
        } else {
            $img_type = '.' . $type;
        }
        return $save_path . Tools::getGuider("image") . $img_type;
    } else {
        return $save_path . Tools::getGuider("image") . "." . $type;
    }
}

/**
 * 数据格式化   将字段值为null统一转换为空字符串''
 * @param $data
 */
function formatData($data)
{
    if (is_array($data) && empty($data)) {
        return (object)[];
    }

    if (is_object($data) && count((array)$data)) {
        $data = $data->toArray();
    }

    if (is_array($data)) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = formatData($value);
            } else {
                if ($value === null || $value === 'null') {
                    $data[$key] = '';
                }
            }
        }
    }

    return $data;
}

/**
 * 根据场合ID获取param_val集合
 * @param $oid
 */
function getMatchParamByOid($oid)
{
    $matchParam = config('goods.match_param.occasion');
    return $matchParam[$oid] ?? [];
}

/**
 * 分页
 * @param $data
 * @param int $page
 * @param int $pageSize
 * @return array
 */
function getPage($data, $page = 1, $pageSize = 16)
{
    $lastPage = ceil(count($data) / $pageSize);
    $r = array_slice($data, ($page-1) * $pageSize, $pageSize);
    return [
        'total' => count($data),
        'per_page' => $pageSize,
        'current_page' => $page,
        'last_page' => $lastPage,
        'data' => $r
    ];
}