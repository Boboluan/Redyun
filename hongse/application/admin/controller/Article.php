<?php

namespace app\admin\controller;
use app\service\ArticleService;
use org\Qiniu;
use think\Db;

class Article extends Base
{
    /**
     * @return \think\response\Json|\think\response\View
     * 数据列表
     */
    public function Index()
    {
        if(request()->isAjax()){
            $params = input();
            return ApiReturn(ArticleService::ArticleList($params));
        }
        return view();
    }


    /**
     * @return \think\response\Json
     * 文章状态
     */
    public function ArticleStatus()
    {
        $params = input();
        return ApiReturn(ArticleService::Status($params));
    }


    /**
     * @return \think\response\Json
     * 文章推荐状态
     */
    public function ArticleTuiStatus()
    {
        $params = input();
        return ApiReturn(ArticleService::TuiStatus($params));
    }


    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function ArticleTableEdit()
    {
        $params = input();
        return ApiReturn(ArticleService::TableEdit($params));
    }



    /**
     * @return
     * 文章编辑
     */
    public function ArticleEdit()
    {
        if(request()->isPost()){
            $params = input();
//            $params['province'] = OptionalQuery('region',['id'=>$params['city']])['pid'];
            return ApiReturn(ArticleService::Edit($params));
        }
        $id = input('id');
        $data = Db::name('article')->where(['id'=>$id])->find();
        return view('article/edit_article',['article'=>$data,'allPlace'=>PlaceDataList()]);
    }



    /**
     * @return
     * 文章添加
     */
    public function ArticleAdd()
    {
        if(request()->isPost()){
            $params = input();
            return ApiReturn(ArticleService::Add($params));
        }
        return view('article/add_article',['allPlace'=>PlaceDataList()]);
    }


    /**
     * @return \think\response\Json
     * 文章删除
     */
    public function ArticleDelete()
    {
        $params = input();
        return ApiReturn(ArticleService::Delete($params));
    }



    /**
     * @return \think\response\Json
     * 批量删除
     */
    public function BatchDelete()
    {
        $params = input();
        return ApiReturn(ArticleService::DeleteMore($params));
    }



}
