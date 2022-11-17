<?php

namespace app\api\controller;

use app\api\controller\Common as ApiBaseCommon;
use app\models\UsercodeModel;
use app\service\UserService;
use think\Request;
use think\Cache;

class Userlogic extends ApiBaseCommon
{



    /**
     * @return
     * 用户登录
     *
     */
    public function UserLogin(Request $request)
    {
        $Params = input();
        return ApiReturn(UserService::Login($Params));
    }



    /**
     *
     * 用户注册
     */
    public function UserRegister()
    {
        $Params = input();
        return ApiReturn(UserService::Register($Params));
    }


    /**
     * @param Request $request
     * @return \think\response\Json
     * 用户收藏列表
     */
    public function UserCollect()
    {
        $Params = input();
        $token = request()->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::Collect($Params,$userToken));
    }


    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * 用户添加收藏
     */
    public function UserAddCollect(Request $request)
    {
        $Params = input();
        $token = $request->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::AddCollect($Params,$userToken));
    }


    /**
     * @param Request $request
     * @return \think\response\Json
     * 用户取消收藏
     */
    public function UserDelCollect(Request $request)
    {
        $Params = input();
        $token = $request->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::DelCollect($Params,$userToken));
    }


    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 用户浏览足迹列表
     */
    public function UserBrowHistory(Request $request)
    {
        $Params = input();
        $token = $request->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::HistoryList($Params,$userToken));
    }


    /**
     * @param Request $request
     * @return \think\response\Json
     * 用户浏览记录记录
     */
    public function BrowHistory(Request $request)
    {
        $Params = input();
        $token = $request->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::AddHistory($Params,$userToken));
    }

    /**
     *
     * 用户修改资料
     */
    public function UserInfoEdit(Request $request)
    {
        $Params = input();
        $token = $request->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::InfoEdit($Params,$userToken));
    }



    /**
     *
     * 当前用户资料查询
     */
    public function UserInfoData(Request $request){

        $token = $request->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::UserInfo($userToken));
    }



    /**
     *
     * 执行发送验证码
     */
    public function SendMsgCode()
    {
        $round = mt_rand(1000,9999);
        $params = input();
        $phone = trim($params['phone']);
        if(empty($params['phone']) || empty($params['scene']))
        {
            return ApiReturn(DataReturn(['code'=>-1,'msg'=>'参数不得为空']));
        }
        $username="xd000878";
        $password="xd00087806";
        $mobiles="$phone";
        $content="您的验证码为：".$round."【红色革命】";
        $send =  Send::send($username,$password,$mobiles,$content);
        if($send){
            $key = $phone.$params['scene'];
            Cache::set($key,$round,0);//写入缓存
            return ApiReturn(DataReturn('发送成功',0));
        }
        return ApiReturn(DataReturn('发送失败',-1));
    }




    /**
     * @return
     * 账号安全
     */
    public function accountSecurity(Request $request)
    {
        $Params = input();
        $token = $request->header('token');
        $tokenCheck = self::CheckUserStatus($token);
        $userToken = $tokenCheck['data'];
        if($tokenCheck['code']!=0){
            return ApiReturn($tokenCheck);
        }
        return ApiReturn(UserService::accountService($Params,$userToken));
    }



    /**
     * @return \think\response\Json
     * 忘记密码
     */
    public function ForgetPassword()
    {
        $Params = input();
        return ApiReturn(UserService::ForgetService($Params));
    }



}
