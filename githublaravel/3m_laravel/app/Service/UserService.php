<?php

namespace App\Service;

use App\Service\TokenService as Token;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserService
{

    /**
     * @param array $Params
     * @return array
     * 测试返回用例
     */
    public static function List()
    {
        $List = User::query()->get();
        return $List;
    }


    /**
     * @param array $Params
     * @return array|\Illuminate\Support\MessageBag
     * 验证登录
     */
    public static function ValidateLoginData(array $Params)
    {
        $rule = [
            //验证字段
            'username'=>'required|max:20',
            'password'=>'required|min:6',
        ];
        $message = [
            'username.required'=>'用户名不能为空',
            'password.required'=>'密码不能为空',
        ];
        $Validate = Validator::make($Params,$rule,$message);
        if($Validate->fails())  return $Validate->errors();
        return self::CheckUser($Params);
    }



    /**
     * @param $Params
     * @return array
     * 检查用户信息
     */
    public static function CheckUser($Params)
    {
        $password = md5($Params['password'].'3mApi');
        $User = User::query()->where(['username'=>$Params['username']])->first();
        !empty($User)?$User->toArray():[];
        if(empty($User)) return DataReturn('该用户不存在',-1);
        if($password != $User['password'])  return DataReturn('密码错误',-1);
//        $User['token'] = (new Token())->createToken($User['id']);
        request()->session()->push("user.id",$User['id']);
        request()->session()->push("user.name",$User['username']);
        $JumpUrl = '/admin/index/index';
        return DataReturn('登录成功',0,$JumpUrl);
    }



    /**
     * @param array $Params
     * @return array|\Illuminate\Support\MessageBag
     * 用户修改密码
     */
    public static function SetPass(array $Params)
    {
        $rule = [
//            'token'=>'required',
            'old_password'=>'required',
            'password'=>'required|min:6',
            'password_confirmation'=>'required|same:password',//不为空,两次密码是否相同
        ];
        $message = [
//            'token.required'=>'未设置token',
            'old_password.required'=>'原始密码不能为空',
            'password.required'=>'密码不能为空',
            'password_confirmation.required'=>'确认密码不能为空',
            'password_confirmation.same'=>'密码与确认密码不匹配',
        ];
        $Validate = Validator::make($Params,$rule,$message);
        if($Validate->fails())  return $Validate->errors();
//        $Uid = (new Token())->validateToken($Params['token']);
        $check = User::query()->where(['id'=>$Params['Uid']])->first()->toArray();
//        $user = request()->session()->get('user');
//        dump($a);die();
        if(md5($Params['old_password'].'3mApi')!=$check['password']){
            return DataReturn('原始密码不正确',-1);
        }
        $newPassword = md5($Params['password'].'3mApi');
        $PassArr = ['password'=>$newPassword];
        $query = User::where(['id'=>$Params['Uid']])->Update($PassArr);
        return  $query ? DataReturn('修改成功',200):DataReturn('修改失败',-1);
    }


    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     * 详细
     */
    public static function Info(array $params)
    {
      $Info = User::query()->find($params['uid']);
      return $Info;
    }




}
