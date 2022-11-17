<?php

namespace app\service;

use app\models\MemberModel as Member;
use think\Db;
use think\Exception;

class MemberService
{

    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 后台数据列表
     */
    public static function MemberList( array $Params = [])
    {
        $map = [];
        $page = isset($Params['page']) ?$Params['page']:1;
        $limit = isset($Params['limit']) ?$Params['limit']:10;
        if(isset($Params['key'])  &&!empty($Params['key'])) $map['nickname'] = ['like',"%".$Params['key']."%"];
        if(isset($Params['phone'])  &&!empty($Params['phone'])) $map['phone'] = ['like',"%".$Params['phone']."%"];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['>= time',$Params['start']];
        if(isset($Params['end'])  &&!empty($Params['end'])    &&isset($Params['start'])    &&!empty($Params['start'])) $map['create_time'] = ['<= time',$Params['end']];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[$Params['start'],$Params['end']]];
        $List  = Member::where($map)->page($page,$limit)->order('create_time desc')->select()->toArray();
        $Count = count($List);
        return ['msg'=>'','code'=>0,'count'=>$Count,'data'=>$List];
    }



    /**
     * @param array $Params
     * @return array
     * 用户状态
     */
    public static function Status(array $Params = [])
    {
        $query =  Member::where(['id'=>$Params['id']])->Update(['status'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 单元格编辑
     */
    public static function TableEdit(array $Params = [])
    {
        $query =  Member::where(['id'=>$Params['id']])->Update([$Params['field']=>$Params['value']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 用户编辑
     */
    public static function Edit(array $Params = [])
    {
        $Data = [
            'title'=>$Params['title'],
            'content'=>htmlspecialchars_decode($Params['content']),
            'writer'=>$Params['writer'],
            'city'  =>$Params['city'],
            'province'=> OptionalQuery('region',['id'=>$Params['city']])['pid'],
            'cover'=>$Params['cover_pic'],
            'type' =>$Params['type']
        ];
        if(empty($Data['cover'])){
            $Data['cover'] = OptionalQuery('Member',['id'=>$Params['id']])['cover'];
        }
        Db::startTrans();
        try {
            Member::where(['id'=>$Params['id']])->Update($Data);
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
     * 用户添加
     */
    public static function Add(array $Params = [])
    {
        $Data = [
            'title'=>$Params['title'],
            'content'=>htmlspecialchars_decode($Params['content']),
            'create_time'=>time(),
            'writer'=>$Params['writer'],
            'city' =>$Params['city'],
            'province'=> OptionalQuery('region',['id'=>$Params['city']])['pid'],
            'cover'=>$Params['cover_pic'],
            'type' =>$Params['type']
        ];
        if(empty($Data['cover'])){
            return DataReturn('请上传封面', 0);
        }
        Db::startTrans();
        try {
            $Query = Member::insertGetId($Data);
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
        $query =  Member::where(['id'=>$Params['id']])->delete();
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
        $query =  Member::where($where)->delete();
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }


    /**
     * @param array $Params
     * @return array
     * 批量禁用
     */
    public static function HiddenMore(array $Params)
    {
        if(!empty($Params['ids'])){
            $where['id'] = ['in',$Params['ids']];
        }else{
            $where = "1=1";
        }
        $query =  Member::where($where)->update(['status'=>0]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }


    /**
     * @param array $Params
     * @return array
     * 批量启用
     */
    public static function UseAll(array $Params)
    {
        if(!empty($Params['ids'])){
            $where['id'] = ['in',$Params['ids']];
        }else{
            $where = "1=1";
        }
        $query =  Member::where($where)->update(['status'=>1]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }


}
