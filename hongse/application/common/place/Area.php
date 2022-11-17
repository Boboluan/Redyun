<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2018/6/26
 * Time: 20:56
 */
namespace app\common\place;
use think\Db;
class Area
{
    /**
     * province 省
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function province(){
        $province = Db::name('area')
            ->where('pid',1)
            ->field('district_id,pid,district')
            ->order('district_id asc')
            ->select();
        return $province;
    }
    /**
     * place 地区三级联动
     * @return
     */
    public function area(){
        $id = input('param.id');
        $area = Db::name('area')
            ->where('pid',$id)
            ->field('district_id,pid,district')
            ->order('district_id asc')
            ->select();
        return ['code'=>200,'msg'=>$area];
    }




    /**
     * province 省
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function provinceRegion(){
        $province = Db::name('region')
            ->where('pid',100000)
            ->field('id,pid,name')
            ->select();
        return $province;
    }
    /**
     * place 地区三级联动
     *
     * @return
     */
    public function areaRegion(){
        $id = input('param.id');
        $area = Db::name('region')
            ->where('pid',$id)
            ->field('id,pid,name')
            ->select();
        return ['code'=>200,'msg'=>$area];
    }


    public function areaRegiones($province_id){
        $id = $province_id;
        $area = Db::name('region')
            ->where('pid',$id)
            ->field('id,pid,name')
            ->select();
        return $area;
    }



}
