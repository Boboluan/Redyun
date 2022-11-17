<?php

namespace app\api\controller;

use app\service\VideoService;

class Video extends Common
{

    /**
     * @return \think\response\Json
     * 视频列表
     */
    public function VideoList()
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
            return ApiReturn(VideoService::VideoList($userToken));
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
    public function VideoInfo()
    {
        if(request()->isPost()){
            $Params = input('video_id');
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
            return ApiReturn(DataReturn('获取成功',0,VideoService::VideoInfo($Params,$userToken)));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
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
            return ApiReturn(VideoService::CateVideoList($Params,$userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }











}
