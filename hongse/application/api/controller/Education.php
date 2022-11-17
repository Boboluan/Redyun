<?php

namespace app\api\controller;

use app\service\EducationService;

class Education extends Common
{

    /**
     * @return \think\response\Json
     * 视频列表
     */
    public function EducationVideoList()
    {
        if(request()->isPost()){
            $token = request()->header('token');
            if(!empty($token)) {
                $tokenCheck = self::CheckUserStatus($token);
                $userToken = $tokenCheck['data'];
                if ($tokenCheck['code'] != 0) {
                    return ApiReturn($tokenCheck);
                }
            }else{
                $userToken = '';
            }
            return ApiReturn(EducationService::VideoList($userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }




    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 视频详情
     */
    public function EducationVideoInfo()
    {
        if(request()->isPost()){
            $Params = input('id');
            $token = request()->header('token');
            if(!empty($token)) {
                $tokenCheck = self::CheckUserStatus($token);
                $userToken = $tokenCheck['data'];
                if ($tokenCheck['code'] != 0) {
                    return ApiReturn($tokenCheck);
                }
            }else{
                $userToken = '';
            }
            return ApiReturn(DataReturn('获取成功',0,EducationService::VideoInfo($Params,$userToken)));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }



    /**
     * @return \think\response\Json
     * 省市区全数据
     */
    public function location()
    {
        $list = LocationAllData();
        return ApiReturn(DataReturn('success',0,$list));
    }



    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 某个单独分类下的视频
     */
    public function CategoryVideoList()
    {
        if(request()->isPost()){
            $Params = input();
            $token = request()->header('token');
            if(!empty($token)) {
                $tokenCheck = self::CheckUserStatus($token);
                $userToken = $tokenCheck['data'];
                if ($tokenCheck['code'] != 0) {
                    return ApiReturn($tokenCheck);
                }
            }else{
                $userToken = '';
            }
            return ApiReturn(EducationService::CateVideoList($Params,$userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }




    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 思政课更多列表页
     */
    public function MoreVideoList()
    {
        if(request()->isPost()){
            $Params = input();
            $token = request()->header('token');
            if(!empty($token)) {
                $tokenCheck = self::CheckUserStatus($token);
                $userToken = $tokenCheck['data'];
                if ($tokenCheck['code'] != 0) {
                    return ApiReturn($tokenCheck);
                }
            }else{
                $userToken = '';
            }
            return ApiReturn(EducationService::MoreVideoList($Params,$userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }








}
