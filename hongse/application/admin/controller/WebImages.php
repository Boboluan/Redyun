<?php

namespace app\admin\controller;
use app\models\WebimagModel as webImg;
use think\Db;
use think\Exception;

class WebImages extends Base
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
            if(isset($Params['key'])  &&!empty($Params['key'])) $map['title'] = ['like',"%".$Params['key']."%"];
            if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['>= time',$Params['start']];
            if(isset($Params['end'])  &&!empty($Params['end'])    &&isset($Params['start'])    &&!empty($Params['start'])) $map['create_time'] = ['<= time',$Params['end']];
            if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[$Params['start'],$Params['end']]];
            $List = webImg::where($map)->page($page,$limit)->select()->toArray();
            foreach ($List as &$item){
                $item['images'] = explode(",",$item['images']);
            }
            $count = webImg::where($map)->count();
            return ApiReturn(['msg'=>'','code'=>0,'count'=>$count,'data'=>$List]);
        }
        return view('webimages/index');
    }



    /**
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 图片更新
     */
    public function Edit()
    {
        if(request()->isPost()){
            $params = input();
            $arr = [
                'images'=>$params['images'],
            ];
            if(empty($arr['images'])){
                $arr['images'] = OptionalQuery('web_images',['id'=>$params['id']])['images'];
            }
            Db::startTrans();
            try {
                $query = webImg::where(['id'=>$params['id']])->update($arr);
                if(!$query){
                    throw new Exception("The catch error");
                }
                Db::commit();
                return ApiReturn(DataReturn('更新成功',200));
            }catch (\Exception $e){
                Db::rollback();
                return ApiReturn(DataReturn('更新失败',100));
            }
        }
        $id = input("id");
        return view('webimages/edit',[
            'Exhibition'=>webImg::where(['id'=>$id])->find()->toArray(),
        ]);
    }






}
