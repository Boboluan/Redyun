<?php

namespace app\admin\controller;
use app\models\DisplayModel;
use app\models\DisplaystandModel;
use app\models\ExhibitionModel;
use app\models\StadiumModel;
use app\service\ExhibitionService;
use think\Db;

class Exhibition extends Base
{

    /**
     * @return \think\response\Json|\think\response\View
     * 数据列表
     */
    public function Index()
    {
        if(request()->isAjax()){
            $params = input();
            return ApiReturn(ExhibitionService::ExhibitionList($params));
        }
        return view();
    }


    /**
     * @return \think\response\Json
     * 展览状态
     */
    public function ExhibitionStatus()
    {
        $params = input();
        return ApiReturn(ExhibitionService::Status($params));
    }


    /**
     * @return \think\response\Json
     * 展览推荐状态
     */
    public function ExhibitionTuiStatus()
    {
        $params = input();
        return ApiReturn(ExhibitionService::recommendStatus($params));
    }


    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function ExhibitionTableEdit()
    {
        $params = input();
        return ApiReturn(ExhibitionService::TableEdit($params));
    }



    /**
     * @return
     * 展览编辑
     */
    public function ExhibitionEdit()
    {
        if(request()->isPost()){
            $params = input();
            return ApiReturn(ExhibitionService::Edit($params));
        }
        $id = input('id');
        $data = ExhibitionModel::where(['id'=>$id])->find()->toArray();
        if(!empty($data['end_time'])) $data['end_time']  =  date('Y-m-d',$data['end_time']);
        if(!empty($data['start_time'])) $data['start_time']  = date('Y-m-d',$data['start_time']);
        $ExhibitionList = ExhibitionModel::where(['status'=>1])->select()->toArray();
        return view('exhibition/edit',['Exhibition'=>$data,'Stadium'=>StadiumList(),'ExhibitionList'=>$ExhibitionList]);
    }



    /**
     * @return
     * 展览添加
     */
    public function ExhibitionAdd()
    {
        if(request()->isPost()){
            $params = input();
//            dump($params);die();
            return ApiReturn(ExhibitionService::Add($params));
        }
        $ExhibitionList = ExhibitionModel::where(['status'=>1])->select()->toArray();
        return view('exhibition/add',['Stadium'=>StadiumList(),'ExhibitionList'=>$ExhibitionList]);
    }


    /**
     * @return \think\response\Json
     * 展览删除
     */
    public function ExhibitionDelete()
    {
        $params = input();
        return ApiReturn(ExhibitionService::Delete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function BatchDelete()
    {
        $params = input();
        return ApiReturn(ExhibitionService::DeleteMore($params));
    }




    /*--------------------展区start-------------------------*/

    /**
     * @return \think\response\View
     * 展区列表页
     */
    public function DisplayArea()
    {
        $exhibition_id = input('exhibition_id');//展览id
        //逆向
        $area_id = input('area_id');
        if(!empty($area_id)){
            $exhibition_id =  OptionalQuery('display_area',['id'=>$area_id])['exhibition_id'];
        }
        return view('exhibition/displayarea',['exhibition_id'=>$exhibition_id]);
    }



    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 展区列表数据
     */
    public function displayData()
    {
        $Params = input();
        return ApiReturn(ExhibitionService::displayList($Params));
    }



    /**
     * @return \think\response\Json
     * 展区状态
     */
    public function displayStatus()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displayareaStatus($params));
    }




    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function displayTableEdit()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displayareaTableEdit($params));
    }



    /**
     * @return
     * 展区编辑
     */
    public function displayEdit()
    {
        if(request()->isPost()){
            $params = input();
            return ApiReturn(ExhibitionService::displayareaEdit($params));
        }
        $id = input('id');
        $data = DisplayModel::where(['id'=>$id])->find();
//        dump($data);die();
        return view('exhibition/displayarea_edit',['Exhibition'=>$data]);
    }



    /**
     * @return
     * 展区添加
     */
    public function displayAdd()
    {
        if(request()->isPost()){
            $params = input();
//            dump($params);die;
            return ApiReturn(ExhibitionService::displayareaAdd($params));
        }
        $exhibition_id = input('exhibition_id');
        return view('exhibition/displayarea_add',['exhibition_id'=>$exhibition_id]);
    }


    /**
     * @return \think\response\Json
     * 展区删除
     */
    public function displayDelete()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displayareaDelete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function displayareaBatchDelete()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displayareaDeleteMore($params));
    }



/*--------------------------------展位 start--------------------------------------*/


    /**
     * @return \think\response\View
     * 展位列表页
     */
    public function DisplayStand()
    {
        $area_id = input('area_id');//展区id
        return view('exhibition/displaystand',['area_id'=>$area_id]);
    }



    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 展位列表数据
     */
    public function displaystandData()
    {
        $Params = input();
        return ApiReturn(ExhibitionService::displaystandList($Params));
    }



    /**
     * @return \think\response\Json
     * 展位状态
     */
    public function displaystandStatus()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displaystandStatus($params));
    }




    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function displaystandTableEdit()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displaystandTableEdit($params));
    }



    /**
     * @return
     * 展位编辑
     */
    public function displaystandEdit()
    {
        if(request()->isPost()){
            $params = input();
            return ApiReturn(ExhibitionService::displaystandEdit($params));
        }
        $id = input('id');
        $data = DisplaystandModel::where(['id'=>$id])->find();
        return view('exhibition/displaystand_edit',['Exhibition'=>$data]);
    }



    /**
     * @return
     * 展位添加
     */
    public function displaystandAdd()
    {
        if(request()->isPost()){
            $params = input();
            return ApiReturn(ExhibitionService::displaystandAdd($params));
        }
        $area_id = input('area_id');
        return view('exhibition/displaystand_add',['area_id'=>$area_id]);
    }


    /**
     * @return \think\response\Json
     * 展位删除
     */
    public function displaystandDelete()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displaystandDelete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function displaystandBatchDelete()
    {
        $params = input();
        return ApiReturn(ExhibitionService::displaystandDeleteMore($params));
    }



}
