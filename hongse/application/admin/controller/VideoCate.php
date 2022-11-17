<?php

namespace app\admin\controller;

use app\models\VideoCategoryModel;

use app\service\VideoService;

class VideoCate extends Base
{

    /**
     * @return \think\response\Json|\think\response\View
     *
     */
    public function Index()
    {
        if(request()->isAjax()){
            $Params = input();
            $List = VideoCategoryModel::VideoCateList($Params);
            $count = $List['count'];
            $ResultList = $List['List'];
            return ApiReturn(['msg'=>'','code'=>0,'data'=>$ResultList,'count'=>$count]);
        }
        return view('videocate/index');
    }




    /**
     * @return \think\response\Json
     * 分类状态
     */
    public function VideocateStatus()
    {
        $params = input();
        return ApiReturn(VideoService::CateStatus($params));
    }


    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function VideocateTableEdit()
    {
        $params = input();
        return ApiReturn(VideoService::CateTableEdit($params));
    }



}
