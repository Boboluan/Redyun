<?php

namespace app\api\controller;

use app\service\StadiumService;
use think\Db;
use think\Exception;

class Stadium extends Common
{

    /**
     * @return \think\response\Json
     *
     * 场馆的列表
     */
    public function StadiumList()
    {
        if(request()->isPost()){
            $Params = input();
            $token = request()->header('token');
            if(!empty($token)){
                $tokenCheck = self::CheckUserStatus($token);
                $userToken = $tokenCheck['data'];
                if($tokenCheck['code']!=0){
                    return ApiReturn($tokenCheck);
                }
            }else{
                $userToken = '';
            }
            return ApiReturn(StadiumService::List($Params,$userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }


    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 省市列表
     */
    public function RegionList()
    {
        if(request()->isPost()){
            return ApiReturn(StadiumService::Region());
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }


    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 场馆详情
     */
    public function StadiumInfo()
    {
        if(request()->isPost()){
            $Params = input();
            $token = request()->header('token');
            if(!empty($token)){
                $tokenCheck = self::CheckUserStatus($token);
                $userToken = $tokenCheck['data'];
                if($tokenCheck['code']!=0){
                    return ApiReturn($tokenCheck);
                }
            }else{
                $userToken = '';
            }
            return ApiReturn(StadiumService::Info($Params,$userToken));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }





    /*关于场馆地区于与省市区数据不对称的处理方式*/
    public function Query()
    {
        Db::startTrans();
        try {
            $StadiumList = Db::name('stadium')->select();
            $region = Db::name('region')->where('pid', '<>', 0)->select();
            foreach ($region as $item) {
                foreach ($StadiumList as $value) {
                    if (!empty($value['province']) || !empty($value['city'])) {
                        if ($value['province'] == $item['name']) {
                            Db::name('stadium')->where('id', $value['id'])->update(['province' => $item['id']]);
                        }
                        if ($value['city'] == $item['name']) {
                            Db::name('stadium')->where('id', $value['id'])->update(['city' => $item['id']]);
                        }
                    }
                }
            }
            Db::commit();
            return ApiReturn(DataReturn('success!',0));
        }catch(\Exception $e) {
            Db::rollback();
            return ApiReturn(DataReturn('error!',0));
        }
    }





}
