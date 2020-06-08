<?php
// 应用公共文件
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