<?php

namespace app\service;

use app\models\UserCollectModel;
use app\models\EducationModel;
use app\models\EducationCategoryModel;
use think\Db;
use think\Exception;

class EducationService
{
    /**
     * @param $userToken
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException api思政课课程系列列表
     */
    public static function VideoList($userToken = null)
    {
        $data = [];
        $VideoCategory = EducationCategoryModel::where(['status'=>1])->limit(9)->Select();
        foreach ($VideoCategory as &$item){
            $item['cover'] = http_type().$item['cover'];
            //验证用户收藏
            if(!empty($userToken)){
                $userCollect[] = UserCollectModel::where(['product_id' => $item['id'], 'module' => 'education','user_token'=>$userToken])->find();//检查用户收藏的产品id
                if (!empty($userCollect)) {
                    foreach ($userCollect as $val) {
                        if ($item['id'] == $val['product_id']) {
                            $item['is_collect'] = 'true';
                        } else {
                            $item['is_collect'] = 'false';
                        }
                    }
                }
            }
        }
        $AllCount = EducationModel::Where(['status'=>1])->count();//视频数量
        $data['videocount'] = $AllCount;
        $data['list'] = $VideoCategory;
        $data['banner'] = banner(3);
        return DataReturn('获取成功', 0,$data);
    }




    /**
     * @param $userToken
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api思政课更多列表页
     */
    public static function MoreVideoList($Params,$userToken = null)
    {
        $map = [];
        $page = isset($Params['page']) ?$Params['page']:1;
        $limit = isset($Params['limit']) ?$Params['limit']:9;
        if(isset($Params['keyword'])  &&!empty($Params['keyword'])) $map['title'] = ['like',"%".$Params['keyword']."%"];
        if (isset($Params['start']) && !empty($Params['start'])){//查询某一天内的数据
            $s_time = strtotime($Params['start']);
            $e_time = $s_time + 86400;
            $map['add_time'] = array(array('gt',$s_time),array('lt',$e_time));
        }
//        if(isset($Params['end'])  &&!empty($Params['end'])    &&isset($Params['start'])    &&!empty($Params['start'])) $map['create_time'] = ['<= time',strtotime($Params['end'])];
//        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['create_time'] = ['between time',[strtotime($Params['start']),strtotime($Params['end'])]];
        $data = [];
        $VideoCategory = EducationCategoryModel::where(['status'=>1])->where($map)->page($page,$limit)->Select();
        foreach ($VideoCategory as &$item){
            $item['cover'] = http_type().$item['cover'];
            //token 不为空
            if(!empty($userToken)){
                $userCollect[] = UserCollectModel::where(['product_id' => $item['id'], 'module' => 'education','user_token'=>$userToken])->find();//检查用户收藏的产品id
                if (!empty($userCollect)) {
                    foreach ($userCollect as $val) {
                        if ($item['id'] == $val['product_id']) {
                            $item['is_collect'] = 'true';
                        } else {
                            $item['is_collect'] = 'false';
                        }
                    }
                }
            }
        }
        $AllCount = EducationCategoryModel::Where(['status'=>1])->count();//视频分类数量
        $data['totalNum'] = $AllCount;
        $data['pageSize'] = ceil( $data['totalNum']/$limit);
        $data['NowPage'] = $page;
        $data['list'] = $VideoCategory;
        return DataReturn('获取成功', 0,$data);
    }





    /**
     * @param $id
     * @return array|bool|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 单条数据 详情
     */
    public static function VideoInfo($id,$userToken)
    {
        if(!empty($userToken)){
            recordHistory($userToken,'education',$id);
        }
        $field = 'id,category_id,video,title,create_time,cover';
        $List = EducationModel::Where(['id'=>$id])->field($field)->find()->toArray();
        $List['video'] = http_type().$List['video'];
        $List['cover'] = http_type().$List['cover'];
        //推荐视频
        $where['id'] = ['not in',$id];
        $recommend = EducationModel::Where($where)->where(['is_recommend'=>1])->field('id,category_id,video,title,category_id,cover')->limit(4)->select()->toArray();
        foreach ($recommend as &$value){
            $value['video'] = http_type().$value['video'];
            $value['cover'] = http_type().$value['cover'];
        }
        //更多课程
        $more = EducationModel::Where($where)->where(['category_id'=>$List['category_id']])
            ->field('id,category_id,video,title,category_id,cover,create_time')
            ->order('create_time desc')
            ->select()
            ->toArray();
        foreach ($more as &$item){
            $item['video'] = http_type().$item['video'];
            $item['cover'] = http_type().$item['cover'];
        }
        $List['more'] = $more;
        $List['recommend'] = $recommend;
        return $List;
    }


    /**
     * @param array $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 单独系列下的所有视频
     */
    public static function CateVideoList($params = [],$userToken)
    {
        $Result = [];
        $page  = isset($params['page'])? $params['page']:1;
        $limit = isset($params['limit'])? $params['limit']:20;
        $where = ['category_id'=>$params['category_id'],'status'=>1];
        $field = 'id,category_id,video,title,cover';
        $Result['List']  = EducationModel::where($where)->field($field)->page($page,$limit)->select()->toArray();
        foreach ($Result['List'] as &$value){
            $categoryName = EducationCategoryModel::where(['id'=>$value['category_id']])->value('title');
            if(!empty($userToken)){
                $userCollect[] = UserCollectModel::where(['product_id' => $value['id'], 'module' => 'digitalvenue','user_token'=>$userToken])->find();//检查用户收藏的产品id
                if (!empty($userCollect)) {
                    foreach ($userCollect as $val) {
                        if ($value['id'] == $val['product_id']) {
                            $value['is_collect'] = 'true';
                        } else {
                            $value['is_collect'] = 'false';
                        }
                    }
                }
            }
            $value['video'] = http_type().$value['video'];
            $value['cover'] = http_type().$value['cover'];
            $value['title'] = $value['title'].'——'.$categoryName;
        }
        $totalNum = EducationModel::where($where)->count();
        $Result['totalNum'] = $totalNum;
        $Result['pageSize'] = ceil($totalNum/$limit);
        $Result['NowPage'] = $page;
        return DataReturn('数据获取成功', 0,$Result);
    }


    /*-----------------------------------------------apiend--------------------------------------------*/

    /**
     * @param $Params
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 后台视频列表
     */
    public static function VideoTableList($Params = [])
    {
        $map = [];
        $page = isset($Params['page']) ?$Params['page']:1;
        $limit = isset($Params['limit']) ?$Params['limit']:10;
        if(isset($Params['key'])  &&!empty($Params['key'])) $map['title'] = ['like',"%".$Params['key']."%"];
        if(isset($Params['start'])  &&!empty($Params['start'])) $map['create_time'] = ['>= time',$Params['start']];
        if(isset($Params['end'])  &&!empty($Params['end'])) $map['create_time'] = ['<= time',$Params['end']];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end']) && !empty($Params['end'])) $map['create_time'] = ['between time',[$Params['start'],$Params['end']]];
        $List = EducationModel::Where($map)->page($page,$limit)->order('sort asc,id desc')->select();
        foreach($List as $value){
            $value['cover'] = http_type().$value['cover'];
            $value['category_name'] = EducationCategoryModel::where('id',$value['category_id'])->value('title');
        }
        $Count = EducationModel::where($map)->count();
        return ['code' => 0, 'msg' => '', 'count' => $Count, 'data' => $List];
    }




    /**
     * @param array $Params
     * @return array
     * 视频分类状态
     */
    public static function CateStatus(array $Params = [])
    {
        $query =  EducationCategoryModel::where(['id'=>$Params['id']])->Update(['status'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 分类单元格编辑
     */
    public static function CateTableEdit(array $Params = [])
    {
        $query =  EducationCategoryModel::where(['id'=>$Params['id']])->Update([$Params['field']=>$Params['value']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 视频状态
     */
    public static function Status(array $Params = [])
    {
        $query =  EducationModel::where(['id'=>$Params['id']])->Update(['status'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 视频单元格编辑
     */
    public static function TableEdit(array $Params = [])
    {
        $query =  EducationModel::where(['id'=>$Params['id']])->Update([$Params['field']=>$Params['value']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }






    /**
     * @param array $Params
     * @param $token
     * @return array
     * 添加上传视频
     */
    public static function AddVideo(array $Params)
    {
        DB::startTrans();
        try {
            $Collect = [
                'cover' => $Params['cover_pic'],
                'video' => $Params['video'],
                'title' => $Params['title'],
                'category_id' => $Params['category_id'],
                'create_time' => time(),
                'lecturer'=>$Params['lecturer']
            ];
            $Query = EducationModel::insert($Collect);
            if (!$Query) {
                throw new Exception('the sql query error');
            }
            Db::commit();
            return DataReturn('添加成功', 200);
        } catch (Exception $e) {
            Db::rollback();
            return DataReturn($e->getMessage(), 100);
        }
    }



    /**
     * @param $id
     * @return array|bool|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 单条内容
     */
    public static function GetOne($id)
    {
        $Data =  EducationModel::where(['id'=>$id])->find()->toArray();
        return $Data;
    }



    /**
     * @param array $Params
     * @param $token
     * @return array
     * 编辑视频
     */
    public static function EditVideo(array $Params)
    {
        DB::startTrans();
        try {
            $Collect = [
                'cover' => $Params['cover_pic'],
                'video' => $Params['video'],
                'title' => $Params['title'],
                'category_id' => $Params['category_id'],
                'lecturer'=>$Params['lecturer']
            ];
            if(empty($Collect['video'])){
                $Collect['video'] = OptionalQuery('video',['id'=>$Params['video_id']])['video'];
            }
            $Query = EducationModel::where(['id'=>$Params['video_id']])->Update($Collect);
            if (!$Query) {
                throw new Exception('the sql query error');
            }
            Db::commit();
            return DataReturn('修改成功', 200);
        } catch (Exception $e) {
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
        $query = EducationModel::where(['id'=>$Params['id']])->delete();
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
        $query =  EducationModel::where($where)->delete();
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 视频推荐状态
     */
    public static function RecommendStatus(array $Params = [])
    {
        $query =  EducationModel::where(['id'=>$Params['id']])->Update(['is_recommend'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



}
