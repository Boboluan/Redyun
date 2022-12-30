<?php

namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\UserService as User;
use Illuminate\Support\Facades\Redirect;

class UsersController extends AdminBaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     * 用户登录
     */
    public function CheckLogin()
    {
        return ApiReturn(User::ValidateLoginData(PostData()));
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * 修改密码
     */
    public function SetPassword()
    {
        $Params = PostData();
        return ApiReturn(User::SetPass($Params));
    }


    /**
     * @return
     * 用户列表
     */
    public function UserList()
    {
        return view('user.index',['data'=>User::List()]);
    }


    /**
     * @return
     * 修改页面
     */
    public function UserEdit()
    {
        return view('user.edit',['data'=>User::Info(PostData())]);
    }


    /**
     * 退出
     */
    public function Logout()
    {
        request()->session()->flush();
        return Redirect::to('/admin/login/login');
    }






}
