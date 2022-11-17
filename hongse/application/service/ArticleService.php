<?php

namespace app\service;

use app\models\ArticleModel AS Article;
use think\Db;
use think\Exception;

class ArticleService
{
    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 后台数据列表
     */
    public static function ArticleList( array $Params = [])
    {
        $map = [];
        $page = isset($Params['page']) ?$Params['page']:1;
        $limit = isset($Params['limit']) ?$Params['limit']:10;
        if(isset($Params['key'])  &&!empty($Params['key'])) $map['title'] = ['like',"%".$Params['key']."%"];
        if(isset($Params['type'])  &&!empty($Params['type']))  $map['type'] = $Params['type'];
        if(isset($Params['start'])  &&!empty($Params['start']))   $map['create_time'] = ['>= time',$Params['start']];
        if(isset($Params['end'])  &&!empty($Params['end'])) $map['create_time'] = ['<= time',$Params['end']];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[$Params['start'],$Params['end']]];
        $List  = Article::where($map)->page($page,$limit)->order('sort asc')->select()->toArray();
        foreach ($List as &$item){
            if($item['type']==1)   $item['type'] = '数字联展';
            if($item['type']==2)   $item['type'] = '思政课';
        }
        $Count = Article::where($map)->count();
        return ['msg'=>'','code'=>0,'count'=>$Count,'data'=>$List];
    }



    /**
     * @param array $Params
     * @return array
     * 文章状态
     */
    public static function Status(array $Params = [])
    {
        $query =  Article::where(['id'=>$Params['id']])->Update(['status'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 文章推荐状态
     */
    public static function TuiStatus(array $Params = [])
    {
        $query =  Article::where(['id'=>$Params['id']])->Update(['is_tui'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }


    /**
     * @param array $Params
     * @return array
     * 单元格编辑
     */
    public static function TableEdit(array $Params = [])
    {
        $query =  Article::where(['id'=>$Params['id']])->Update([$Params['field']=>$Params['value']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 文章编辑
     */
    public static function Edit(array $Params = [])
    {
        $Data = [
            'title'=>$Params['title'],
            'content'=>$Params['content'],
            'writer'=>$Params['writer'],
            'cover'=>$Params['cover_pic'],
            'type' =>$Params['type'],
            'sort' =>$Params['sort'],
            'digest'=>$Params['digest']
        ];
        if(empty($Data['cover'])){
            $Data['cover'] = OptionalQuery('article',['id'=>$Params['id']])['cover'];
        }
        Db::startTrans();
        try {
            Article::where(['id'=>$Params['id']])->Update($Data);
            Db::commit();
            return DataReturn('操作成功', 200);
        } catch (\Exception $e) {
            Db::rollback();
            return DataReturn($e->getMessage(), 100);
        }
    }




    /**
     * @param array $Params
     * @return array
     * 文章添加
     */
    public static function Add(array $Params = [])
    {
        $Data = [
            'title'=>$Params['title'],
            'content'=>$Params['content'],
            'create_time'=>time(),
            'writer'=>$Params['writer'],
            'cover'=>$Params['cover_pic'],
            'type' =>$Params['type'],
            'sort' =>isset($Params['sort']) ?? 100,
            'digest'=>$Params['digest']
        ];
        if(empty($Data['cover'])){
            return DataReturn('请上传封面', 0);
        }
        Db::startTrans();
        try {
            $Query = Article::insertGetId($Data);
            if(empty($Query)){
                throw new Exception("error");
            }
            Db::commit();
            return DataReturn('操作成功', 200);
        } catch (\Exception $e) {
            Db::rollback();
            return DataReturn($e->getMessage(), 100);
        }
    }


    /**
     * @param array $Params
     * @return array
     * 删除
     */
    public static function Delete(array $Params = [])
    {
        $query =  Article::where(['id'=>$Params['id']])->delete();
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }


    /**
     * @param array $Params
     * @return array
     * 批量删除
     */
    public static function DeleteMore(array $Params = [])
    {
        $where['id'] = ['in',$Params['ids']];
        $query =  Article::where($where)->delete();
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }


    /*-----------------------------------------------------apiStart-------------------------------------------------------------*/
    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 文章列表 文章类型(1：数字联展 2：思政课)
     */
    public static function ApiArticleList( array $Params = [])
    {
        $map = [];
        $data = [];
        $page = isset($Params['page']) ?$Params['page']:1;
        $limit = isset($Params['limit']) ?$Params['limit']:10;
        if(isset($Params['keyword'])  &&!empty($Params['keyword'])) $map['title'] = ['like',"%".$Params['keyword']."%"];
        if(isset($Params['start'])  &&!empty($Params['start']))   $map['create_time'] = ['>= time',strtotime($Params['start'])];
        if(isset($Params['end'])  &&!empty($Params['end'])) $map['create_time'] = ['<= time',strtotime($Params['end'])];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[strtotime($Params['start']),strtotime($Params['end'])]];
        if(empty($Params['type']))  return DataReturn('文章类型不能为空!',-1);
        $data['List']  = Article::where($map)->where(['status'=>1])->where(['type'=>$Params['type']])->page($page,$limit)->order('create_time desc')->select()->toArray();
        foreach ($data['List'] as &$datum)
        {
            if(!strstr($datum['cover'],'http'))
            {
                $datum['cover'] = http_type().$datum['cover'];
            }
            $datum['create_time'] = explode("-",$datum['create_time']);
        }
        $data['totalNum'] = Article::where($map)->where(['status'=>1])->where(['type'=>$Params['type']])->count();//总条数
        $data['pageSize'] = ceil($data['totalNum']/$limit);//页数
        $data['nowPage']  = $page;//当前页面
        return DataReturn('获取数据成功!',0,$data);
    }




    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 数字联展文章顶部
     */
    public static function ApiRecommend()
    {
        $data = [];
        $Big = [];
        $Small = [];
        $topArticle = Article::where(['status'=>1,'is_tui'=>1,'type'=>1])->field('id,title,create_time,cover')->order('sort asc')->limit(6)->select()->toArray();
        $recommend = array_slice($topArticle,0,3);//带图片展示的文章
        $data['list'] = array_slice($topArticle,3,3);//右边的列表
        foreach ($data['list'] as &$val){
            $val['create_time'] = explode("-",$val['create_time']);
        }
        foreach ($recommend as $key =>&$item)
        {
            if(!empty($item['cover']))
            {
                if(!strstr($item['cover'],'http'))
                {
                    $item['cover'] = http_type().$item['cover'];
                }
            }
            if($key==0){
                array_push($Big,$item);
            }else{
                array_push($Small,$item);
            }
        }
        $data['big'] = $Big;
        $data['small'] = $Small;
        $data['count'] = Article::where(['status'=>1])->count();
        return DataReturn('数据获取成功!',0,$data);
    }




    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 思政文章顶部推荐
     */
    public static function EducationApiRecommend()
    {
        $data = [];
        $data['recommend'] = Article::where(['status'=>1,'is_tui'=>1,'type'=>2])->field('id,title,create_time,cover')->order('sort asc')->limit(4)->select();
        $data['count'] = Article::where(['status'=>1,'type'=>2])->count();
        foreach ($data['recommend'] as $key =>&$item)
        {
            if(!empty($item['cover']))
            {
                if(!strstr($item['cover'],'http'))
                {
                    $item['cover'] = http_type().$item['cover'];
                }
            }
        }
        return DataReturn('数据获取成功!',0,$data);
    }




    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 文章详情
     */
    public static function ApiArticleInfo( array $Params = [])
    {
        $data = [];
        $field = 'id,title,content,create_time,digest';
        $where['id'] = ['not in',$Params['article_id']];
        $data['info'] = Article::where('id',$Params['article_id'])->find();
        $data['info']['content'] = htmlspecialchars_decode($data['info']['content']);
        $data['otherArticle'] = Article::where($where)->order('sort','asc')->field($field)->limit(4)->select();
        return DataReturn('数据获取成功!',0,$data);
    }


}
