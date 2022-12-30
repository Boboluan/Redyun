<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{

    /**
     * @param $uid
     * @return string
     * 生成token
     */
    public function createToken($uid)
    {
        $key = config('app')['JwtKey'];
        $time = time();
        $payload = array(
            "iss" => "",
            "aud" => "",
            "iat" => $time,
            "nbf" => $time,
            "exp" => $time+86400*7,
            "uid" => $uid
        );

        $token = JWT::encode($payload,$key,"HS256");

        return $token;
    }




    /**
     * @param $token
     * @return string
     * 验证token
     */
    public function validateToken($token)
    {
        $key = config('app')['JwtKey'];
        try {
            $decoded = JWT::decode($token, new Key($key,"HS256"));
//            dump($decoded);die();
//            return DataReturn('','',$decoded->uid);
            return $decoded->uid;
        }catch (\Exception $e){
            return 'token过期';
        }
    }


    /**
     * @return string
     * 算法生成token
     */
    public function definedToken()
    {
        $salt = $this->GetRandNumber();
        $str=md5(uniqid(md5(microtime(true)),true));
        $token=sha1($str.$salt);
        return$token;
    }


    /**
     * @param int $len
     * @return string
     * 随机数列
     */
    public function GetRandNumber($len = 6)
    {
        $chars=array(
            "1","2",
            "3","4","5","6","7","8","9"
        );
        $charsLen=count($chars)-1;
        shuffle($chars);
        $output="";
        for($i=0;$i<$len;$i++)
        {
            $output.=$chars[mt_rand(0,$charsLen)];
        }
        return$output;
    }



}
