<?php

namespace app\service;

use app\models\UserCollectModel;
use app\models\VideoModel;
use app\models\VideoCategoryModel;
use think\Db;
use think\Exception;

class VideoService
{
    /**
     * @param $userToken
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException api视频列表
     */
    public static function VideoList($userToken)
    {
        $data = [];
        $TOPBig = [];
        $TOPSmall = [];
        $VideoCategory = VideoCategoryModel::VideoCateListInfo();
        $data['data'] = [];
        foreach ($VideoCategory as $key=>$item){
            $field = 'id,category_id,video,title,cover';
            $videoList = VideoModel::Where(['status'=>1,'category_id'=>$item['id']])->field($field)->limit(4)->select();
            foreach ($videoList as $keys=> &$value){
                //token 不为空
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
                $value['video'] = http_type(). $value['video'];
                $value['cover'] = http_type(). $value['cover'];
            }
            $data['data'][$key]['category'] = $item;
            $data['data'][$key]['videoList'] = $videoList;
            $data['data'][$key]['count'] = VideoModel::Where(['status'=>1,'category_id'=>$item['id']])->count();
        }
        $TopVideo = VideoModel::Where(['is_top'=>1,'status'=>1])->field($field)->limit(5)->select();
        foreach ($TopVideo as $k=>&$v){
            if(!empty($userToken)){
                $userCollect[] = UserCollectModel::where(['product_id' => $v['id'], 'module' => 'digitalvenue','user_token'=>$userToken])->find();//检查用户收藏的产品id
                if (!empty($userCollect)) {
                    foreach ($userCollect as $val) {
                        if ($v['id'] == $val['product_id']){
                            $v['is_collect'] = 'true';
                        } else {
                            $v['is_collect'] = 'false';
                        }
                    }
                }
            }
            $v['video'] = http_type(). $v['video'];
            $v['cover'] = http_type(). $v['cover'];
            if($k==0){
                $TOPBig[] = $v;
            }else{
                $TOPSmall[] = $v;
            }
        }
        $AllCount = VideoModel::Where(['status'=>1])->count();
        $data['TOPBig'] = $TOPBig;
        $data['TOPSmall'] = $TOPSmall;
        $data['Allcount'] = $AllCount;
        $data['categoryList'] = $VideoCategory;
        $data['banner'] = banner(4);
        return DataReturn('获取成功', 0,$data);
    }


//   /uploads/images/20220318/d04f14bc16b9445faf3655f704511275.png

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
        if(isset($Params['category_id'])  &&!empty($Params['category_id'])) $map['category_id'] = $Params['category_id'];
        if(isset($Params['key'])  &&!empty($Params['key'])) $map['title'] = ['like',"%".$Params['key']."%"];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['add_time'] = ['>= time',$Params['start']];
        if(isset($Params['end'])  &&!empty($Params['end'])    &&isset($Params['start'])    &&!empty($Params['start'])) $map['add_time'] = ['<= time',$Params['end']];
        if(isset($Params['start'])  &&!empty($Params['start'])  &&isset($Params['end'])    &&!empty($Params['end']))   $map['add_time'] = ['between time',[$Params['start'],$Params['end']]];
        $List = VideoModel::Where($map)->page($page,$limit)->order('sort asc,id desc')->select()->toArray();
        foreach($List as &$value){
            $value['add_time'] = date('Y-m-d',$value['add_time']);
            $value['cover'] = http_type().$value['cover'];
            $value['category_name'] = VideoCategoryModel::where('id',$value['category_id'])->value('title');
        }
        $Count = VideoModel::where($map)->count();
        return ['code' => 0, 'msg' => '', 'count' => $Count, 'data' => $List];
    }




    /**
     * @param array $Params
     * @return array
     * 视频分类状态
     */
    public static function CateStatus(array $Params = [])
    {
        $query =  VideoCategoryModel::where(['id'=>$Params['id']])->Update(['status'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 分类单元格编辑
     */
    public static function CateTableEdit(array $Params = [])
    {
        $query =  VideoCategoryModel::where(['id'=>$Params['id']])->Update([$Params['field']=>$Params['value']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 视频状态
     */
    public static function Status(array $Params = [])
    {
        $query =  VideoModel::where(['id'=>$Params['id']])->Update(['status'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 视频单元格编辑
     */
    public static function TableEdit(array $Params = [])
    {
        $query =  VideoModel::where(['id'=>$Params['id']])->Update([$Params['field']=>$Params['value']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
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
        $Data =  VideoModel::where(['id'=>$id])->find()->toArray();
        return $Data;
    }


    /**
     * @param $id
     * @return array|bool|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 单条数据
     */
    public static function VideoInfo($id,$userToken = null)
    {
        $field = 'id,category_id,video,title,add_time,cover,audio';
        $List = VideoModel::Where(['id'=>$id])->field($field)->find();
        if(!empty($List['video'])) $List['video'] = http_type().$List['video'];
        if(!empty($List['audio'])) $List['audio'] = http_type().$List['audio'];
        if(!empty($List['cover'])) $List['cover'] = http_type().$List['cover'];
        $List['add_time'] = date('Y-m-d',$List['add_time']);
        //其他同分类视频
        $where['id'] = ['not in',$id];
        $where['category_id'] = $List['category_id'];
        $otherVideo = VideoModel::Where($where)->field('id,category_id,video,title,category_id,cover,audio')->limit(4)->select()->toArray();
        foreach ($otherVideo as &$value){
            if(!empty($userToken)){
                recordHistory($userToken,'digitalvenue',$id);
                $userCollect[] = UserCollectModel::where(['product_id' => $value['id'], 'module' => 'digitalvenue','user_token'=>$userToken])->find();//检查用户收藏的产品id
                if (!empty($userCollect)) {
                    foreach ($userCollect as &$val) {
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
            $value['audio'] = http_type().$value['audio'];
            $category_name = VideoCategoryModel::where(['id'=>$value['category_id']])->value('title');
        }
        $List['category_name'] = $category_name;
        $List['otherVideo'] = $otherVideo;
        return $List;
    }



    /**
     * @param array $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 某个单独分类下的所有视频
     */
    public static function CateVideoList($params = [],$userToken)
    {
        $Result = [];
        $page  = isset($params['page'])? $params['page']:1;
        $limit = isset($params['limit'])? $params['limit']:18;
        $where = ['category_id'=>$params['category_id'],'status'=>1];
        $field = 'id,category_id,video,title,cover,sort';
        $Result['List']  = VideoModel::where($where)->field($field)->page($page,$limit)->order('sort asc,add_time desc')->select()->toArray();
        foreach ($Result['List'] as &$value){
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
        }
        $totalNum = VideoModel::where($where)->count();
        $Result['Count'] = $totalNum;
        $Result['pageSize'] = ceil($totalNum/$limit);
        $Result['NowPage'] = $page;
        $Result['list']['category_name'] = VideoCategoryModel::where(['id'=>$params['category_id']])->value('title');
        return DataReturn('数据获取成功', 0,$Result);
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
                'video' => $Params['video']??'',
                'title' => $Params['title'],
                'category_id' => $Params['cate_id'],
                'audio'=>$Params['audio']??'',
                'add_time' => time(),
            ];
            $Query = VideoModel::insert($Collect);
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
                'video' => $Params['video']??'',
                'title' => $Params['title'],
                'category_id' => $Params['cate_id'],
                'audio'=>$Params['audio']??'',
            ];
            if(empty($Collect['video'])){
                $Collect['video'] = OptionalQuery('video',['id'=>$Params['video_id']])['video'];
            }
            $Query = VideoModel::where(['id'=>$Params['video_id']])->Update($Collect);
//            if (!$Query) {
//                throw new Exception('the sql query error');
//            }
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
        $query = VideoModel::where(['id'=>$Params['id']])->delete();
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
        $query =  VideoModel::where($where)->delete();
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 视频置顶状态
     */
    public static function TopStatus(array $Params = [])
    {
        if($Params['num']==1){
            $Count = OptionalQueryCount('video',['is_top'=>1]);
            if($Count >= 5) {
                return DataReturn('置顶最多5个',100);
            }
        }
        $query =  VideoModel::where(['id'=>$Params['id']])->Update(['is_top'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }



    /**
     * @param array $Params
     * @return array
     * 视频首页推荐状态
     */
    public static function IndexStatus(array $Params = [])
    {
//        if($Params['num']==1){
//            $Count = OptionalQueryCount('video',['is_index'=>1]);
//            if($Count >= 5) {
//                return DataReturn('推荐最多5个',100);
//            }
//        }
        $query =  VideoModel::where(['id'=>$Params['id']])->Update(['is_index'=>$Params['num']]);
        return $query ? DataReturn('操作成功',200):DataReturn('操作失败',100);
    }




}
