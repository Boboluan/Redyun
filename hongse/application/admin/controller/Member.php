<?php

namespace app\admin\controller;

use app\service\MemberService;
use think\Db;

class Member extends Base
{

    /**
     * @return \think\response\Json|\think\response\View
     * 数据列表
     */
    public function Index()
    {
        if(request()->isAjax()){
            $params = input();
            return ApiReturn(MemberService::MemberList($params));
        }
        return view();
    }


    /**
     * @return \think\response\Json
     * 用户状态
     */
    public function MemberStatus()
    {
        $params = input();
        return ApiReturn(MemberService::Status($params));
    }




    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function MemberTableEdit()
    {
        $params = input();
        return ApiReturn(MemberService::TableEdit($params));
    }



    /**
     * @return
     * 用户编辑
     */
    public function MemberEdit()
    {
        if(request()->isPost()){
            $params = input();
            $params['province'] = OptionalQuery('region',['id'=>$params['city']])['pid'];
            return ApiReturn(MemberService::Edit($params));
        }
        $id = input('id');
        $data = Db::name('Member')->where(['id'=>$id])->find();
        return view('Member/edit_Member',['Member'=>$data,'allPlace'=>PlaceDataList()]);
    }




    /**
     * @return \think\response\Json
     * 用户删除
     */
    public function MemberDelete()
    {
        $params = input();
        return ApiReturn(MemberService::Delete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function BatchDelete()
    {
        $params = input();
        return ApiReturn(MemberService::DeleteMore($params));
    }


    /**
     * @return \think\response\Json
     * 批量禁用
     */
    public function BatchHidden()
    {
        $params = input();
        return ApiReturn(MemberService::HiddenMore($params));
    }



    /**
     * @return \think\response\Json
     * 批量启用
     */
    public function BatchUse()
    {
        $params = input();
        return ApiReturn(MemberService::UseAll($params));
    }


}
