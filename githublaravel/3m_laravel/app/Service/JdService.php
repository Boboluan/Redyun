<?php

namespace App\Service;

use Illuminate\Support\Facades\Cache;

class JdService
{

    private $serverUrl = 'https://api.jd.com/routerjson';

    private $appKey = '111D72313B260C737142823051EF8EBE';

    private $appSecret = '40f68917dd514a3699e415e6902c30d1';

    private $code = 'RTMDWg';

    private $source = 'jingdong';

    private $area = '1-1-1-1';

    private $channel = '1';

    private $InfoSet = '1';

    private $FieldSet = '102';

    private $token = '627036196b264ce29937aebcd3d6dc61ji3m';

    private $refresh_token = '12cdf78a7d624f389660e06b4ef20ce7tjjz';

    private $Key = 'access_token';

    /**
     * 获取token返回数据
     */
    //"{"access_token":"627036196b264ce29937aebcd3d6dc61ji3m",
    //"expires_in":86400,
    //"refresh_token":"12cdf78a7d624f389660e06b4ef20ce7tjjz",
    //"scope":"snsapi_base",
    //"open_id":"6HoX7Hocc29_PcF2S_AASY-qkO4KPqCv7_DDp9_REow",
    //"uid":"7060954021","time":1655196220537,
    //"token_type":"bearer",
    //"code":0,
    //"xid":"o*AASqJxKimKxs6T0Iv829UwzCZmM5NrLtwNmFlfSDxCN_3039ytU"}"


    /**
     * 刷新token 2022-6-15 15:50
     */
    //array:10 [
    //"access_token" => "627036196b264ce29937aebcd3d6dc61ji3m"
    //"expires_in" => 86400
    //"refresh_token" => "12cdf78a7d624f389660e06b4ef20ce7tjjz"
    //"scope" => "snsapi_base"
    //"open_id" => "6HoX7Hocc29_PcF2S_AASY-qkO4KPqCv7_DDp9_REow"
    //"uid" => "7060954021"
    //"time" => 1655279330590
    //"token_type" => "bearer"
    //"code" => 0
    //"xid" => "o*AASqJxKimKxs6T0Iv829UwzCZmM5NrLtwNmFlfSDxCN_3039ytU"
    //]

    /**
     * @param string $Sku
     * 批量获取商品价格
     */
    public function ProductPrice($Sku = '')
    {

        include_once ("../extends/jos-php-open-api-sdk-2.0/JdSdk.php");

        $c = new \JdClient();

        $c->appKey = $this->appKey;

        $c->appSecret = $this->appSecret;

        $c->accessToken = $this->token;

        $c->serverUrl = $this->serverUrl;

        $req = new \JdpricesGetRequest();

        $req->setArea($this->area);

        $req->setSkuId($Sku);

        $req->setChannel($this->channel);

        $req->setPriceFieldSet($this->FieldSet);

        $req->setSource($this->source);

        $req->setPriceInfoSet($this->InfoSet);

        $resp = $c->execute($req,$c->accessToken);

        return $resp;
    }


    /**
     * 获取token
     */
    private function getToken()
    {
        $url = "https://open-oauth.jd.com/oauth2/access_token?app_key=$this->appKey&app_secret=$this->appSecret&grant_type=authorization_code&code=$this->code";
        return request_curl($url,'','GET');
    }




    /**
     * 获取code
     */
    private function getCode()
    {
        $url =  "https://open-oauth.jd.com/oauth2/to_login?app_key=111D72313B260C737142823051EF8EBE&response_type=code&redirect_uri=3mjdtest.cbcrfund.com&state=20180416&scope=snsapi_base";
        return request_curl($url,'','GET');
    }



    /**
     * 刷新授权
     */
    private  function RefreshAuthorization()
    {
        $url = "https://open-oauth.jd.com/oauth2/refresh_token?app_key=$this->appKey&app_secret=$this->appSecret&grant_type=refresh_token&refresh_token=$this->refresh_token";
        $result =  request_curl($url,'','GET');
        $result = json_decode($result,true);
        //写入缓存
        Cache::put($this->Key,$result['access_token'],$result['expires_in']);
        return $result;
    }


    /**
     * 检测token&续期token
     */
    private function CheckToken()
    {
        if(!Cache::has($this->Key)){
            $this->RefreshAuthorization();
        }else{
            $this->token = Cache::get($this->Key);
        }
    }


}
