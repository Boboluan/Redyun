<?php

namespace app\service;

use app\models\ExhibitionModel;
use app\models\StadiumModel as Stadium;
use app\models\RegionModel as Region;
use app\models\UserCollectModel;
use think\Db;
use think\Exception;

class StadiumService
{
    /**
     * @param $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 场馆列表
     *
     */
    public static function List($Params = [],$userToken)
    {
        $where = [];
        $Result = [];
        if(!empty($Params['province']) && isset($Params['province'])) $where['province'] = $Params['province'];
        if(!empty($Params['city'])     && isset($Params['city']))     $where['city'] = $Params['city'];
        if(!empty($Params['keyword'])  && isset($Params['keyword']))  $where['building_name'] = ['like',"%".$Params['keyword']."%"];
        $page  = isset($Params['page']) ? $Params['page']:1;
        $limit = isset($Params['limit'])? $Params['limit']:12;
        if(!empty($Params['keyword']) && empty($Params['province'])){
            $where['building_name'] = ['like',"%".$Params['keyword']."%"];
        }
        if(!empty($Params['keyword']) && !empty($Params['province'])){
            $where['building_name'] = ['like',"%".$Params['keyword']."%"];
        }
        $field = 'id,building_name,cover';
        $Result['List'] = Stadium::where($where)->where(['status'=>1])->field($field)->page($page,$limit)->order('id asc')->select();
        $Result['banner'] = banner(5);
        //判断当前用户是否收藏
        foreach ($Result['List'] as $item){
            if(!empty($userToken)) {
                $userCollect[] = UserCollectModel::where(['product_id' => $item['id'], 'module' => 'venue','user_token'=>$userToken])->find();//检查用户收藏的产品id
                if (!empty($userCollect)) {
                    foreach ($userCollect as $val) {
                        if ($item['id'] == $val['product_id']){
                            $item['is_collect'] = 'true';
                        } else {
                            $item['is_collect'] = 'false';
                        }
                    }
                }
            }
            $item['cover'] = http_type().$item['cover'];
        }
        $totalNum = Stadium::where($where)->where(['status'=>1])->count();
        $Result['pageSize'] = ceil($totalNum/$limit);
        $Result['totalNum'] = $totalNum;
        $Result['NowPage'] = $page;
        return DataReturn('获取数据成功',0,$Result);
    }



    /**
     * @param $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api革命场馆详情数据
     */
    public static function Info($Params,$userToken)
    {
        $List = Stadium::where(['id'=>$Params['Stadium_id']])->find()->toArray();

        if(!empty($List['outer_img'])){
            $List['outer_img'] = explode(",",$List['outer_img']);
            foreach($List['outer_img'] as &$val){
                $val = http_type().$val;
            }
        }
        if(!empty($List['inside_img'])){
            $List['inside_img'] = explode(",",$List['inside_img']);
            foreach($List['inside_img'] as &$val){
                $val = http_type().$val;
            }
        }
        if(!empty($List['cover']))  $List['cover'] = http_type().$List['cover'];

        //下方最近展出(展览)
        $recommend = ExhibitionModel::where(['status'=>1,'stadium_id'=>$Params['Stadium_id']])->field('id,title,cover,start_time,end_time')->order('sort asc')->limit(4)->select();
        !empty($recommend)?$recommend = $recommend->toArray():$recommend = [];
        if(!empty($recommend)){
            foreach ($recommend as &$item){
                if (!empty($item['start_time']) && !empty($item['end_time'])) {

                    $holding_time = date('Y-m-d', $item['start_time']) . '——' . date('Y-m-d', $item['end_time']);

                } else if(!empty($item['start_time'] && empty($item['end_time']))){

                    $holding_time = date('Y-m-d', $item['start_time']) . '——' . '至今';

                }else if(empty($item['start_time']) && empty($item['end_time'])){

                    $holding_time = '长期';
                }
                $item['holding_time'] = $holding_time;
                //收藏(展览)
                if(!empty($userToken)) {
                    recordHistory($userToken,'venue',$Params['Stadium_id']);//该展览的浏览记录
                    $userCollect[] = UserCollectModel::where(['product_id' => $item['id'], 'module' => 'exhibition','user_token'=>$userToken])->find();//检查用户收藏的产品id
                    if (!empty($userCollect)) {
                        foreach ($userCollect as $val) {
                            if ($item['id'] == $val['product_id']){
                                $item['is_collect'] = 'true';
                            } else {
                                $item['is_collect'] = 'false';
                            }
                        }
                    }
                }
            }
            $List['recommend'] = $recommend;
        }
        return DataReturn('获取数据成功',0,$List);
    }


    //-----------------------------------------------------apiend---------------------------------------------------------------//

    /**
     * @param $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 省市数据
     */
    public static function Region()
    {
        $field = 'id,name,level';
        $List = Region::where('pid',100000)->field($field)->select()->toArray();
        foreach ($List as $key =>&$item){
            $item['city'] = Region::where(['pid'=>$item['id']])->field($field)->select()->toArray();
        }
        return DataReturn('success',0,$List);
    }



    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 数据列表
     */
    public static function StadiumList( array $Params = [])
    {
        $map = [];
        $page = isset($Params['page']) ?$Params['page']:1;
        $limit = isset($Params['limit']) ?$Params['limit']:10;
        if(isset($Params['key'])  &&!empty($Params['key'])) $map['building_name'] = ['like',"%".$Params['key']."%"];
        if(isset($Params['province'])  &&!empty($Params['province'])) $map['province'] = $Params['province'];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['>= time',$Params['start']];
        if(isset($Params['end'])  &&!empty($Params['end'])    &&isset($Params['start'])    &&!empty($Params['start'])) $map['create_time'] = ['<= time',$Params['end']];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[$Params['start'],$Params['end']]];
        $List  = Stadium::where($map)->page($page,$limit)->order('sort asc,id desc')->field('otherimg',true)->select()->toArray();
        $Count = Stadium::where($map)->count();
        return ['msg'=>'','code'=>0,'count'=>$Count,'data'=>$List];
    }



    /**
     * @param array $Params
     * @return array
     * 场馆状态
     */
    public static function Status(array $Params = [])
    {
        try {
            Db::startTrans();
            $query =  Stadium::where(['id'=>$Params['id']])->update(['status'=>$Params['num']]);
            if(!$query){
                throw new Exception('修改失败');
            }
            Db::commit();
            return DataReturn('操作成功',200);
        }catch (\Exception $e){
            Db::rollback();
            return DataReturn('操作失败',100);
        }

    }


    /**
     * @param array $Params
     * @return array
     * 场馆推荐状态
     */
    public static function Recommend(array $Params = [])
    {
        try {
            Db::startTrans();
            $query =  Stadium::where(['id'=>$Params['id']])->Update(['is_recommend'=>$Params['num']]);
            if(!$query){
                throw new Exception('修改失败');
            }
            Db::commit();
            return DataReturn('操作成功',200);
        }catch (\Exception $e){
            Db::rollback();
            return DataReturn('操作失败',100);
        }
    }


    /**
     * @param array $Params
     * @return array
     * 单元格编辑
     */
    public static function TableEdit(array $Params = [])
    {
        $query =  Stadium::where(['id'=>$Params['id']])->Update([$Params['field']=>$Params['value']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 场馆编辑
     */
    public static function Edit(array $Params = [])
    {
        $Data = [
            'cover'=>$Params['cover_pic'],
            'building_describe'=>htmlspecialchars_decode($Params['content']),
            'city'      => $Params['city'],
            'province'  => $Params['province'],
            'outer_img'  =>isset($Params['outer_img'])?$Params['outer_img']:'',
            'inside_img'  =>isset($Params['inside_img'])?$Params['inside_img']:'',
        ];
        $cover = Stadium::where(['id'=>$Params['id']])->find();
        if(empty($Data['cover'])){
            $Data['cover'] = $cover['cover'];
        }
        Db::startTrans();
        try {
            Stadium::where(['id'=>$Params['id']])->Update($Data);
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
     * 场馆添加
     */
    public static function Add(array $Params = [])
    {
        $Data = [
            'building_name'    =>$Params['building_name'],
            'building_location'=>$Params['building_location'],
            'building_phone'   =>$Params['building_phone'],
            'start_time'       =>$Params['start_time'],
            'building_web'     =>$Params['building_web'],
            'online_url'       =>$Params['online_url'],
            'city'             =>$Params['city'],
            'building_describe'=>htmlspecialchars_decode($Params['content']),
            'cover'            =>$Params['cover_pic'],
            'create_time'      =>time(),
            'province'         =>$Params['province'],
            'otherimg'         =>isset($Params['otherimg'])?$Params['otherimg']:'',
        ];

        if(empty($Data['cover'])){
            return DataReturn('请上传封面', 0);
        }
        Db::startTrans();
        try {
            $Query = Stadium::insertGetId($Data);
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
        $query =  Stadium::where(['id'=>$Params['id']])->delete();
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
        $query =  Stadium::where($where)->delete();
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



}
