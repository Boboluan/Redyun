<?php

namespace app\common\validate;

use think\Validate;


class Uservaildate extends Validate
{
    protected $rule = [
        'username'  => 'require|max:15',
        'nickname'  => 'require|max:15',
        'province'  => 'require',
        'city'      => 'require',
        'headimg'   => 'require',
        'password'=> 'require|min:6',
        'phone'=>'require|max:11|/^1[3-8]{1}[0-9]{9}$/',
        'verifCode'=>'require|number|max:4|min:4',
        'affirmPass'=>'require|min:6|confirm:password',
        'setPasscode'=>'require|number|max:4|min:4',
        'newpassword'=>'require|min:6',
        'reqirPass'=>'require|min:6|confirm:newpassword',
    ];

    protected $message = [
        'province.require'=>'省份必须',
        'city.require'    =>'城市必须',
        'headimg.require' =>'头像必须',
        'username.require' => '用户名必须',
        'nickname.require' => '姓名必须',
        'password.require'=> '密码必须',
        'phone.require'=>'手机号必须',
        'phone.max'=>'手机号最大长度为11',
        'phone'=>'输入正确的手机号码',
        'affirmPass.confirm' => '两次密码不一致',
        'verifCode.require'=>'验证码必须',
        'reqirPass.confirm' => '两次密码不一致',
        'setPasscode.require'=>'验证码必须',
    ];

    protected $scene = [
        'login'  =>  ['username','password'],
        'register'=> ['phone','password','verifCode','affirmPass'],
        'setPass' => ['newpassword','setPasscode','reqirPass','phone'],
        'updateUserinfo'=>['nickname','phone','province','city','headimg']
    ];

}
