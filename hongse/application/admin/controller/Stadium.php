<?php

namespace app\admin\controller;
use app\service\StadiumService;
use org\Qiniu;
use think\Db;

class Stadium extends Base
{

    /**
     * @return \think\response\Json|\think\response\View
     * 数据列表
     */
    public function Index()
    {
        if(request()->isAjax()){
            $params = input();
            return ApiReturn(StadiumService::StadiumList($params));
        }
        return view('',['province'=>ProvinceDataList()]);
    }


    /**
     * @return \think\response\Json
     * 场馆状态
     */
    public function StadiumStatus()
    {
        $params = input();
        return ApiReturn(StadiumService::Status($params));
    }


    /**
     * @return \think\response\Json
     * 场馆推荐状态
     */
    public function StadiumRecommend()
    {
        $params = input();
        return ApiReturn(StadiumService::Recommend($params));
    }

    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function StadiumTableEdit()
    {
        $params = input();
        return ApiReturn(StadiumService::TableEdit($params));
    }


    /**
     * @return
     * 场馆编辑
     */
    public function StadiumEdit()
    {
        if(request()->isPost()){
            $params = input();
            return ApiReturn(StadiumService::Edit($params));
        }
        $id = input('id');
        $data = Db::name('stadium')->where(['id'=>$id])->find();
        $area = new \app\common\place\Area;
        $province = $area->provinceRegion();
        $citydata = $area->areaRegiones($data['province']);
//        dump($data);die();
        return view('edit',['stadium'=>$data,'province'=>$province,'citydata'=>json_encode($citydata)]);
    }



    /**
     * @return
     * 场馆添加
     */
    public function StadiumAdd()
    {
        if(request()->isPost()){
            $params = input();
            return ApiReturn(StadiumService::Add($params));
        }
        $area = new \app\common\place\Area;
        $province = $area->provinceRegion();
        return view('add',['province'=>$province]);
    }


    /**
     * @return \think\response\Json
     * 场馆删除
     */
    public function StadiumDelete()
    {
        $params = input();
        return ApiReturn(StadiumService::Delete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function BatchDelete()
    {
        $params = input();
        return ApiReturn(StadiumService::DeleteMore($params));
    }




    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 返回省级下属城市数据
     */
    public function City()
    {
        $Province_id = input('Province_id');
        return ApiReturn(DataReturn(CityDataList($Province_id)));
    }




}
