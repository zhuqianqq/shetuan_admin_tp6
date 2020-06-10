<?php
declare (strict_types=1);

namespace app\util;
use TbkItemInfoGetRequest;
use TbkItemShareConvertRequest;
use TopClient;

/**
 * TaobaoApi
 * Class JwtUtil
 * @package app\util
 */
class TaobaoApiUtil
{
    const APPKEY = '25528546';
    const SECRETKEY = '832eed6eea5085863bc58e3e7be803ff';
    /**
     * 淘宝客商品详情查询（简版）
     * @param $num_iids
     */
   public static function getItemInfo($num_iids)
   {
       require_once(__DIR__ . '/../../extend/taobao_sdk/TopSdk.php'); //引用淘宝开放平台 API SDK
       $c = new TopClient;
       $c->appkey = static::APPKEY;
       $c->secretKey = static::SECRETKEY;
       $c->format = 'json';
       $req = new TbkItemInfoGetRequest;
       $req->setNumIids($num_iids);
       $req->setPlatform("1");
       $ip = Tools::getClientIp();
       $req->setIp($ip);
       $resp = $c->execute($req);
       if (empty($resp)) {
           return false;
       }
       if (isset($resp->code)) {
           return false;
       }
       if (empty($resp->results->n_tbk_item)) {
           return false;
       }
       $data = $resp->results->n_tbk_item;
       return $data;
   }

    /**
     * 高佣转链接口API
     * 淘宝客-推广者-商品三方分成链接转换
     * @param $num_iids
     */
    public static function getShareConvert($num_iid)
    {
        $siteid = 770380;
        $adzoneid = 107639450222;
        $uid = 831377483;
        $url = "https://api.taokouling.com/tkl/TbkPrivilegeGet?apikey=DTEjfbMiOA&itemid=" . $num_iid . "&siteid=" . $siteid . "&adzoneid=" . $adzoneid . "&uid=" . $uid;
        $r = Tools::curlPost($url);
        if (empty($r['result'])) {
            return false;
        }
        return $r['result']['data'];
    }
}
