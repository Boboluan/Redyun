<?php

namespace app\api\controller;

use app\service\ExhibitionService;

class Exhibition extends Common
{

    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 展览列表
     */
    public function ExhibitionList(): \think\response\Json
    {
        if(request()->isPost()){
            $token = request()->header('token');
            $Params = input();
            if(!empty($token)) {
                $tokenCheck = self::CheckUserStatus($token);
                $userToken = $tokenCheck['data'];
                if ($tokenCheck['code'] != 0) {
                    return ApiReturn($tokenCheck);
                }
            }else{
                $userToken = '';
            }
            return ApiReturn(ExhibitionService::ApiExhibitionList($userToken,$Params));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }



    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 更多列表页
     */
    public function MoreExhibitionList()
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
            return ApiReturn(ExhibitionService::MoreList($Params,$userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }





    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 展览详情
     */
    public function ExhibitionInfo()
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
            return ApiReturn(ExhibitionService::ApiExhibitionInfo($Params,$userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }


    /**/
    public function EndRecommend()
    {
        $Params = input();
        return ApiReturn(ExhibitionService::EndRecommendList($Params));
    }



}
