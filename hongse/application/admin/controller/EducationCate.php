<?php

namespace app\admin\controller;

use app\models\EducationCategoryModel;

use app\service\EducationService;
use think\Db;
use think\Exception;

class EducationCate extends Base
{

    /**
     * @return \think\response\Json|\think\response\View
     *
     */
    public function Index()
    {
        if(request()->isAjax()){
            $List = EducationCategoryModel::VideoCateList();
            $count = count($List);
            return ApiReturn(['msg'=>'','code'=>0,'data'=>$List,'count'=>$count]);
        }
        return view('educationcate/index');
    }




    /**
     * @return \think\response\Json
     * 分类状态
     */
    public function VideocateStatus()
    {
        $params = input();
        return ApiReturn(EducationService::CateStatus($params));
    }


    /**
     * @return \think\response\Json
     * 单元编辑
     */
    public function VideocateTableEdit()
    {
        $params = input();
        return ApiReturn(EducationService::CateTableEdit($params));
    }



    /**
     * @return
     * 添加分类
     */
    public function InsertCate()
    {
        if(request()->isPost()){
            Db::startTrans();
            try {
                $Params = input();
                $Insert = [
                    'title'=>$Params['title'],
                    'describe'=>$Params['describe'],
                    'cover'=>$Params['cover'],
                    'create_time'=>time()
                ];
                $query =  EducationCategoryModel::insert($Insert);
                Db::commit();
                return ApiReturn(DataReturn('添加成功',200));
            }catch(\Exception $e){
                Db::rollback();
                return ApiReturn(DataReturn('添加失败',100));
            }
        }
        return view('educationcate/add');
    }




    /**
     * @return
     *
     * 修改分类
     */
    public function EditCate()
    {
        if(request()->isPost()){
            Db::startTrans();
            try {
                $Params = input();
                $Insert = [
                    'title'=>$Params['title'],
                    'describe'=>$Params['describe'],
                    'cover'=>$Params['cover'],
//                    'create_time'=>time()
                ];
                if(empty($Insert['cover'])){
                    $Insert['cover'] = OptionalQuery('education_cate',['id'=>$Params['id']])['cover'];
                }
                $query =  EducationCategoryModel::where(['id'=>$Params['id']])->update($Insert);
                Db::commit();
                return ApiReturn(DataReturn('修改成功',200));
            }catch(\Exception $e){
                Db::rollback();
                return ApiReturn(DataReturn('修改失败',100));
            }
        }
        $id = input('id');
        return view('educationcate/edit',[
            'info' => EducationCategoryModel::where(['id'=>$id])->find(),
        ]);
    }


    /**
     * @return \think\response\Json
     * 删除系列/分类
     */
    public function DeleteCate()
    {
        $CateId = input('id');
        $query  = EducationCategoryModel::where(['id'=>$CateId])->delete();
        return $query ? ApiReturn(DataReturn('删除成功',200)):ApiReturn(DataReturn('删除失败',100));
    }





}
