<?php

namespace  app\admin\controller;

use app\models\CustomModel;
use think\response\Json;


class Custom extends Base
{
    /**
     * @return
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 后台数据列表
     */
    public function index()
    {
        if(request()->isAjax()){
            $map = [];
            $page = isset($Params['page']) ?$Params['page']:1;
            $limit = isset($Params['limit']) ?$Params['limit']:10;
            if(isset($Params['key'])  &&!empty($Params['key'])) $map['name'] = ['like',"%".$Params['key']."%"];
            if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['>= time',$Params['start']];
            if(isset($Params['end'])  &&!empty($Params['end'])    &&isset($Params['start'])    &&!empty($Params['start'])) $map['create_time'] = ['<= time',$Params['end']];
            if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[$Params['start'],$Params['end']]];
            $List = CustomModel::where($map)->page($page,$limit)->select()->toArray();
            $count = CustomModel::where($map)->count();
            return ApiReturn(['msg'=>'','code'=>0,'count'=>$count,'data'=>$List]);
        }
        return view('education/information');
    }




    /**
     * @return \think\response\Json
     * 删除
     */
    public function DelInformation()
    {
        $id = input('id');
        $query = CustomModel::where(['id'=>$id])->delete();
        return $query ? ApiReturn(DataReturn('删除成功',200)):ApiReturn(DataReturn('删除失败',100));
    }



    /**
     * @param array $Params
     * @return Json
     * 批量删除
     */
    public function BatchDelete()
    {
        $Params = input();
        $where['id'] = ['in',$Params['ids']];
        $query =  CustomModel::where($where)->delete();
        return $query ? ApiReturn(DataReturn('操作成功',200)):ApiReturn(DataReturn('操作失败',100));
    }



}

