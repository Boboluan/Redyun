<?php

namespace app\api\controller;

//aspx接口调用
class Send
{
    const url ="https://dx.ipyy.net/sms.aspx";
    static function send($account,$password,$mobiles,$content)
    {
        $body=array(
            'action'=>'send',
            'userid'=>'2348',
            'account'=>$account,
            'password'=>$password,
            'mobile'=>$mobiles,
            'content'=>$content,
        );
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, self::url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
?>
