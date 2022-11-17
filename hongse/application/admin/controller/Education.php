<?php

namespace app\admin\controller;
use app\models\EducationModel;
use app\models\EducationCategoryModel;
use app\service\EducationService;

class Education extends Base
{

    /**
     * @return \think\response\Json|\think\response\View
     * 后台主页
     */
    public function Index()
    {
        if(request()->isAjax()){
            $Params = input();
            return  ApiReturn(EducationService::VideoTableList($Params));
        }
        return view('education/index');
    }


    /**
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 添加视频
     */
    public function AddVideo()
    {
        if(request()->isPost()){
            $Params = input();
            return  ApiReturn(EducationService::AddVideo($Params));
        }
        return view('education/add_video',['cate'=>EducationCategoryModel::SelectCate()]);
    }



    /**
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 编辑视频
     */
    public function EditVideo()
    {
        if(request()->isPost()){
            $Params = input();
            return  ApiReturn(EducationService::EditVideo($Params));
        }
        $id = input('id');
        return view('education/edit_video',[
            'info'=> EducationService::GetOne($id),
            'cate'=> EducationCategoryModel::SelectCate(),
        ]);
    }




    /**
     * @return \think\response\Json
     * 视频状态
     */
    public function VideoStatus()
    {
        $params = input();
        return ApiReturn(EducationService::Status($params));
    }



    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function VideoTableEdit()
    {
        $params = input();
        return ApiReturn(EducationService::TableEdit($params));
    }




    /**
     * @return \think\response\Json
     * 视频删除
     */
    public function VideoDelete()
    {
        $params = input();
        return ApiReturn(EducationService::Delete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function BatchDelete()
    {
        $params = input();
        return ApiReturn(EducationService::DeleteMore($params));
    }



    /**
     * @return \think\response\Json
     * 推荐状态
     */
    public function Recommend()
    {
        $params = input();
        return ApiReturn(EducationService::RecommendStatus($params));
    }




}
