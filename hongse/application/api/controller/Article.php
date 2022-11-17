<?php

namespace app\api\controller;

use app\service\ArticleService;

class Article extends Common
{

    /**文章类型(1：数字联展 2：思政课)
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 数字联展相关资讯文章列表
     */
    public function ArticleList(): \think\response\Json
    {
        if(request()->isPost()){
            $Params = input();
            return ApiReturn(ArticleService::ApiArticleList($Params));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }


    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 顶部文章(数字联展)
     */
    public function RecommendList()
    {
        if(request()->isPost()){
            $Params = input();
            return ApiReturn(ArticleService::ApiRecommend($Params));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }



    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 顶部文章(思政课)
     */
    public function EducationRecommendList()
    {
        if(request()->isPost()){
            $Params = input();
            return ApiReturn(ArticleService::EducationApiRecommend($Params));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }



    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 文章详情(公用)
     */
    public function ArticleInfo()
    {
        if(request()->isPost()){
            $Params = input();
            return ApiReturn(ArticleService::ApiArticleInfo($Params));
        }
        return ApiReturn(DataReturn('不支持Get方式!',-1000));
    }



}
