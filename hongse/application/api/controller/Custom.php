<?php

namespace app\api\controller;

use app\models\CustomModel;
use think\Db;
use think\Exception;

class Custom extends Common
{

    public function SubInformation()
    {
        if(!request()->isPost()){
            return ApiReturn(DataReturn('只支持post',-1));
        }
        $Params = input();
        $Info = [
            'create_time'=>time(),
            'name'=>trim($Params['name']),
            'phone'=>trim($Params['phone']),
            'email'=>trim($Params['email']),
            'city'=> $Params['city'] ?? '',
            'area'=> $Params['area'] ?? '',
            'remark'=> $Params['remark'] ?? '',
            'company'=> $Params['company'] ?? '',
            'address'=> $Params['address'] ?? '',
            'province'=> $Params['province'] ?? '',
        ];
        if(empty($Info['name']) || empty($Info['phone']) || empty($Info['email'])){
            return ApiReturn(DataReturn('必填项不得为空',-1));
        }
        Db::startTrans();
        try {
            $query = CustomModel::insertGetID($Info);
            if($query<1){
                throw new Exception('error');
            }
            Db::commit();
            return ApiReturn(DataReturn('提交成功',0));
        }catch(\Exception $e){
            Db::rollback();
            return ApiReturn(DataReturn('提交失败',-1));
        }
    }







}
