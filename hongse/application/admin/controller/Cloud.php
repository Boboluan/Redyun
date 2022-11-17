<?php

namespace app\admin\controller;
use app\models\CloudModel;
use think\Db;
use think\Exception;
use think\response\Json;

class Cloud extends Base
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
            $List = CloudModel::where($map)->page($page,$limit)->select()->toArray();
            foreach ($List as &$item){
                $item['images'] = explode(",",$item['images']);
            }
            $count = CloudModel::where($map)->count();
            return ApiReturn(['msg'=>'','code'=>0,'count'=>$count,'data'=>$List]);
        }
        return view('cloud/index');
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
                'images'=> $params['images'],
                'title' => $params['title'],
                'url'   => $params['url'],
                'describe'=>$params['describe'],
                'title_content'=>$params['title_content'],
            ];
            if(empty($arr['images'])){
                $arr['images'] = OptionalQuery('cloud',['id'=>$params['id']])['images'];
            }
            Db::startTrans();
            try {
                $query = CloudModel::where(['id'=>$params['id']])->update($arr);
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
        return view('cloud/edit',[
            'Exhibition'=>CloudModel::where(['id'=>$id])->find()->toArray(),
        ]);
    }



    /**
     * @param array $Params
     * @return Json
     * 推荐状态
     */
    public function Status()
    {
        $Params = input();
        $query =  CloudModel::where(['id'=>$Params['id']])->Update(['status'=>$Params['num']]);
        return $query ? ApiReturn(DataReturn('操作成功',200)):ApiReturn(DataReturn('操作失败',100));
    }



}
