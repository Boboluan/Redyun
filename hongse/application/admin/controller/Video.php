<?php

namespace app\admin\controller;
use app\models\VideoModel;
use app\models\VideoCategoryModel;
use app\service\VideoService;
use org\Qiniu;

class Video extends Base
{

    /**
     * @return \think\response\Json|\think\response\View
     *
     * 后台主页
     */
    public function Index()
    {
        if(request()->isAjax()){
            $Params = input();
            return  ApiReturn(VideoService::VideoTableList($Params));
        }
        return view('video/index',['cateList'=>VideoCategoryModel::VideoCateListInfo()]);
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
            return  ApiReturn(VideoService::AddVideo($Params));
        }
        return view('video/add_video',['cate'=>VideoCategoryModel::VideoCateListInfo()]);
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
            return  ApiReturn(VideoService::EditVideo($Params));
        }
        $id = input('id');
        return view('video/edit_video',[
            'info'=> VideoService::GetOne($id),
            'cate'=> VideoCategoryModel::VideoCateListInfo(),
        ]);
    }




    /**
     * @return \think\response\Json
     * 视频状态
     */
    public function VideoStatus()
    {
        $params = input();
        return ApiReturn(VideoService::Status($params));
    }



    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function VideoTableEdit()
    {
        $params = input();
        return ApiReturn(VideoService::TableEdit($params));
    }




    /**
     * @return \think\response\Json
     * 文章删除
     */
    public function VideoDelete()
    {
        $params = input();
        return ApiReturn(VideoService::Delete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function BatchDelete()
    {
        $params = input();
        return ApiReturn(VideoService::DeleteMore($params));
    }



    /**
     * @return \think\response\Json
     *
     * 置顶状态
     */
    public function Top()
    {
        $params = input();
        return ApiReturn(VideoService::TopStatus($params));
    }



    /**
     * @return \think\response\Json
     * 首页推荐
     */
    public function IndexStatus()
    {
        $params = input();
        return ApiReturn(VideoService::IndexStatus($params));
    }





}
