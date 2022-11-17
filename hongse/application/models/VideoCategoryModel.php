<?php

namespace app\models;

use  think\Model;

class VideoCategoryModel extends Model
{

    protected $name = 'video_category';

    protected $dateFormat = 'Y-m-d';

    public static function VideoCateList(array $Params = [])
    {
        $map = [];
        $Result = [];
        $page = isset($Params['page']) ?$Params['page']:1;
        $limit = isset($Params['limit']) ?$Params['limit']:10;
        if(isset($Params['key'])  &&!empty($Params['key'])) $map['title'] = ['like',"%".$Params['key']."%"];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['>= time',$Params['start']];
        if(isset($Params['end'])  &&!empty($Params['end'])    &&isset($Params['start'])    &&!empty($Params['start'])) $map['create_time'] = ['<= time',$Params['end']];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[$Params['start'],$Params['end']]];
        $Result['List'] = VideoCategoryModel::where($map)->page($page,$limit)->field('title,id,status,create_time')->select()->toArray();
        $Result['count'] = VideoCategoryModel::where($map)->count();
        return $Result;
    }



    public static function VideoCateListInfo()
    {
        $Result = VideoCategoryModel::where(['status'=>1])->field('title,id,status,create_time')->select()->toArray();
        return $Result;
    }


}
