<?php

namespace app\service;

use app\models\DisplayModel;
use app\models\DisplaystandModel;
use app\models\ExhibitionModel as Exhibition;
use app\models\UserCollectModel;
use phpDocumentor\Reflection\Types\Self_;
use think\Db;
use app\models\StadiumModel as Stadium;
use think\Exception;

class ExhibitionService
{

    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 后台数据列表
     */
    public static function ExhibitionList(array $Params = [])
    {
        $map = [];
        $page = isset($Params['page']) ? $Params['page'] : 1;
        $limit = isset($Params['limit']) ? $Params['limit'] : 10;
        if (isset($Params['displayStatus']) && $Params['displayStatus'] != '') $map['display_status'] = (int) $Params['displayStatus'];
        if (isset($Params['key']) && !empty($Params['key'])) $map['title'] = ['like', "%" . $Params['key'] . "%"];
        if (isset($Params['start']) && !empty($Params['start'])) $map['add_time'] = ['>= time', $Params['start']];
        if (isset($Params['end']) && !empty($Params['end'])) $map['add_time'] = ['<= time', $Params['end']];
        if (isset($Params['start']) && !empty($Params['start']) && isset($Params['end']) && !empty($Params['end'])) $map['add_time'] = ['between time', [$Params['start'], $Params['end']]];
        if (isset($Params['province']) && $Params['province'] != '') $map['province'] = $Params['province'];
        if (isset($Params['city']) && $Params['city'] != '') $map['city'] = $Params['city'];
        //省份
        $List = Exhibition::where($map)->page($page, $limit)->order('sort asc,add_time desc')->select()->toArray();
        foreach ($List as &$item) {

            $item['add_time'] = date('Y-m-d', $item['add_time']);

            if(!empty($item['end_time']) && !empty($item['start_time'])){
                $item['end_time']   = date('Y-m-d', intval($item['end_time']));
                $item['start_time'] = date('Y-m-d', intval($item['start_time']));
            }

            if(empty($item['start_time']) && empty($item['end_time'])){
                $item['end_time']   = '长期';
                $item['start_time'] = '长期';
            }
            if(empty($item['end_time']) && !empty($item['start_time'])){
                $item['end_time'] = '至今';
                $item['start_time'] = date('Y-m-d', intval($item['start_time']));
            }

            if(!empty($item['end_time']) && empty($item['start_time'])){
                $item['end_time'] = date('Y-m-d', intval($item['end_time']));
            }

            $stadium = OptionalQuery('stadium', ['id' => $item['stadium_id']]);
            $stadiumAddress = self::stadiumLocation($item['stadium_id']);
            if (empty($item['address']) || $item['address'] == '无') $item['address'] = '';
            $item['location'] = $stadiumAddress . $item['address'];
            $item['stadium'] = $stadium['building_name'];
        }
        $Count = Exhibition::where($map)->count();
        return ['msg' => '', 'code' => 0, 'count' => $Count, 'data' => $List];
    }



    /**
     * @param array $Params
     * @return array
     * 展览状态
     */
    public static function Status(array $Params = [])
    {
        $query = Exhibition::where(['id' => $Params['id']])->Update(['status' => $Params['num']]);
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }



    /**
     * @param array $Params
     * @return array
     * 展览推荐状态
     */
    public static function recommendStatus(array $Params = [])
    {
        $query = Exhibition::where(['id' => $Params['id']])->Update(['is_recommend' => $Params['num']]);
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }


    /**
     * @param array $Params
     * @return array
     * 单元格编辑
     */
    public static function TableEdit(array $Params = [])
    {
        $query = Exhibition::where(['id' => $Params['id']])->Update([$Params['field'] => $Params['value']]);
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }



    /**
     * @param array $Params
     * @return array
     * 展览编辑
     */
    public static function Edit(array $Params = [])
    {
        $Data = [
            'title' => $Params['title'],
            'describe' => $Params['describe'],
            'subtitle' => $Params['subtitle'],
            'stadium_id' => $Params['stadium_id'],
            'insideimg_status'=>$Params['insideimg_status'],
            'cover' => isset($Params['cover']) ? $Params['cover'] : '',
            'start_time'=> $Params['start_time']? strtotime($Params['start_time']):'',
            'end_time'  => $Params['end_time']? strtotime($Params['end_time']):'',
            'recommend_list_one'=>$Params['recommendList_one']??'',
            'recommend_list_two'=>$Params['recommendList_two']??'',
            'recommend_list_three'=>$Params['recommendList_three']??'',
            'insideimg'=> isset($Params['insideimg']) ?$Params['insideimg']:'',
        ];
        if (!empty($Data['stadium_id'])) {
            $stadiumInfo = Stadium::where(['id' => $Data['stadium_id']])->find();
            $Data['province'] = $stadiumInfo['province'];
            $Data['city'] = $stadiumInfo['city'];
        }
        if (empty($Data['cover'])) {
            $Data['cover'] = OptionalQuery('Exhibition', ['id' => $Params['id']])['cover'];
        }
        if (empty($Data['insideimg'])) {
            $Data['insideimg'] = OptionalQuery('Exhibition', ['id' => $Params['id']])['insideimg'];
        }
        Db::startTrans();
        try {
            Exhibition::where(['id' => $Params['id']])->Update($Data);
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
     * 展览添加
     */
    public static function Add(array $Params = [])
    {
        $Data = [
            'title' => $Params['title'],
            'describe' => $Params['describe'],
            'add_time' => time(),
            'cover' => isset($Params['cover']) ? $Params['cover'] : '',
            'start_time'=> $Params['start_time']? strtotime($Params['start_time']):'',
            'end_time'  => $Params['end_time']? strtotime($Params['end_time']):'',
            'stadium_id'=> $Params['stadium_id'],
            'recommend_list_one'=>$Params['recommendList_one']??'',
            'recommend_list_two'=>$Params['recommendList_two']??'',
            'recommend_list_three'=>$Params['recommendList_three']??'',
            'subtitle' => $Params['subtitle'],
            'insideimg_status'=>$Params['insideimg_status'],
            'insideimg'=> $Params['insideimg'],
        ];
        if (!empty($Data['stadium_id'])) {
            $stadiumInfo = Stadium::where(['id' => $Data['stadium_id']])->find();
            $Data['province'] = $stadiumInfo['province'];
            $Data['city'] = $stadiumInfo['city'];
        }
        if (empty($Data['cover'])) {
            return DataReturn('请上传封面', 0);
        }
        Db::startTrans();
        try {
            $Query = Exhibition::insertGetId($Data);
            if (empty($Query)) {
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
        $query = Exhibition::where(['id' => $Params['id']])->delete();
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }


    /**
     * @param array $Params
     * @return array
     * 批量删除
     */
    public static function DeleteMore(array $Params = [])
    {
        $where['id'] = ['in', $Params['ids']];
        $query = Exhibition::where($where)->delete();
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }


    /*-----------------------------------------------------apiStart-------------------------------------------------------------*/
    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 展览列表
     */
    public static function ApiExhibitionList($userToken, array $Params)
    {
        $map = [];
        $data = [];
        $limit = $Params['limit']?? 9;
        $page = $Params['page']?? 1;
        if (isset($Params['displaystatus']) && $Params['displaystatus'] != '') $map['display_status'] = $Params['displaystatus'];
        if (isset($Params['keyword']) && !empty($Params['keyword'])) $map['title'] = ['like', "%" . $Params['keyword'] . "%"];
        if (isset($Params['province']) && !empty($Params['province'])) $map['province'] = $Params['province'];
        if (isset($Params['city']) && !empty($Params['city'])) $map['city'] = $Params['city'];
//        if (isset($Params['start']) && !empty($Params['start'])) { //查询某一天内的数据
//            $s_time = strtotime($Params['start']);
//            $e_time = $s_time + 86400;
//            $map['add_time'] = array(array('gt', $s_time), array('lt', $e_time));
//        }
        if (isset($Params['start']) && !empty($Params['start'])) { //查询开始的展览
            $s_time = strtotime($Params['start']);
            $map['start_time'] = ['egt',$s_time];
        }
        $field = 'id,title,cover,address,start_time,end_time,stadium_id,insideimg';
        $data['List'] = Exhibition::where(['status' => 1])->where($map)->page($page,$limit)->field($field)->order('sort asc,add_time desc')->select()->toArray();
        $data['banner'] = banner(2);
        foreach ($data['List'] as &$datum) {
            //验证用户收藏
            if (!empty($userToken)) {
                $userCollect[] = UserCollectModel::where(['product_id' => $datum['id'], 'module' => 'exhibition', 'user_token' => $userToken])->find(); //检查用户收藏的产品id
                if (!empty($userCollect)) {
                    foreach ($userCollect as $val) {
                        if ($datum['id'] == $val['product_id']) {
                            $datum['is_collect'] = 'true';
                        } else {
                            $datum['is_collect'] = 'false';
                        }
                    }
                }
            }
            $datum['cover'] = http_type() . $datum['cover'];
            $datum['insideimg'] = http_type() . $datum['insideimg'];
            $stadiumAddress = self::stadiumLocation($datum['stadium_id']);
            $datum['location'] = $stadiumAddress;
            //时间判断
            if (!empty($datum['start_time']) && !empty($datum['end_time'])) {

                $holding_time = date('Y-m-d', (int)$datum['start_time']) . '——' . date('Y-m-d', (int)$datum['end_time']);

            } else if(!empty($datum['start_time'] && empty($datum['end_time']))){

                $holding_time = date('Y-m-d', (int)$datum['start_time']) . '——' . '至今';

            }else if(empty($datum['start_time']) && empty($datum['end_time'])){

                $holding_time = '长期';
            }else if(empty($datum['start_time']) && !empty($datum['end_time'])){

                $holding_time = '至今';
            }
            $datum['holding_time'] = $holding_time;
            $data['totalNum'] = Exhibition::where(['status' => 1])->where($map)->count();//总条数
            $data['pageSize'] = ceil( $data['totalNum']/$limit);//总页数
            $data['NowPage'] = $page;
        }
        return DataReturn('获取数据成功!', 0, $data);
    }



    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 更多展览列表服务层
     * author 胡彦勇
     */
    public static function MoreList(array $Params, $userToken)
    {
        $map = [];
        $data = [];
        $page = isset($Params['page']) ? $Params['page'] : 1;
        $limit = isset($Params['limit']) ? $Params['limit'] : 10;
        if (isset($Params['displaystatus']) && $Params['displaystatus'] != '') $map['display_status'] = $Params['displaystatus'];
        if (isset($Params['keyword']) && !empty($Params['keyword'])) $map['title'] = ['like', "%" . $Params['keyword'] . "%"];
        if (isset($Params['province']) && !empty($Params['province'])) $map['province'] = $Params['province'];
        if (isset($Params['city']) && !empty($Params['city'])) $map['city'] = $Params['city'];
        if (isset($Params['start']) && !empty($Params['start'])) { //查询某一天内的数据
            $s_time = strtotime($Params['start']);
            $e_time = $s_time + 86400;
            $map['add_time'] = array(array('gt', $s_time), array('lt', $e_time));
        }

        $field = 'id,title,cover,address,start_time,end_time,stadium_id';
        $data['List'] = Exhibition::where($map)->where(['status' => 1])->page($page, $limit)->field($field)->order('add_time desc')->select()->toArray();

        foreach ($data['List'] as &$datum) {
            if (!empty($userToken)) {
                $userCollect[] = UserCollectModel::where(['product_id' => $datum['id'], 'module' => 'exhibition', 'user_token' => $userToken])->find();
                if (!empty($userCollect)) {
                    foreach ($userCollect as $val) {
                        if ($datum['id'] == $val['product_id']) {
                            $datum['is_collect'] = 'true';
                        } else {
                            $datum['is_collect'] = 'false';
                        }
                    }
                }
            }
            $datum['cover'] = http_type() . $datum['cover'];
            $stadiumAddress = self::stadiumLocation($datum['stadium_id']);
            $datum['location'] = $stadiumAddress;
            //时间判断
            if (!empty($datum['start_time']) && !empty($datum['end_time'])) {

                $holding_time = date('Y-m-d', $datum['start_time']) . '——' . date('Y-m-d', $datum['end_time']);

            } else if(!empty($datum['start_time'] && empty($datum['end_time']))){

                $holding_time = date('Y-m-d', $datum['start_time']) . '——' . '至今';

            }else if(empty($datum['start_time']) && empty($datum['end_time'])){

                $holding_time = '长期';
            }

            $datum['holding_time'] = $holding_time;
        }
        $data['totalNum'] = count($data['List']);
        $data['pageSize'] = ceil($data['totalNum'] / $limit);
        $data['nowPage'] = $page;
        return DataReturn('获取数据成功!', 0, $data);
    }




    /**
     * @param $Params
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 展览结束观看推荐
     */
    public static function EndRecommendList($Params)
    {
        $Ids = [];
        $Return= [];
        $list = [];
        $ExList = [];
        $data = Exhibition::where(['id'=>$Params['exhibition_id']])->field('recommend_list_one,recommend_list_two,recommend_list_three')->find();//设置了推荐
        empty($data)? $data = []: $data = $data->toArray();
        $data = array_unique(array_filter($data));

        if(empty($data)){
            $data = Exhibition::where(['status'=>1])->where('id','<>',$Params['exhibition_id'])->order('sort asc')->field('id')->limit(3)->select();//没有设置推荐，就推荐前三个
            empty($data)? $data = []: $data = $data->toArray();
            foreach ($data as $datum){
                array_push($ExList,$datum['id']);
            }
            $data = $ExList;
            unset($ExList);//释放内存
        }
        if(!empty($data)) {
            foreach ($data as $value) {
                array_push($Ids, $value);
            }
            $Ids = array_unique(array_filter($Ids));
            foreach ($Ids as $item) {
                $list[] = Exhibition::where(['id' => $item])->field('id,title,end_time,start_time,cover,stadium_id')->find()->toArray();
            }
            foreach ($list as &$vr) {
                $vr['location'] = self::stadiumLocation($vr['stadium_id']);
                $vr['cover'] = http_type() . $vr['cover'];

                //时间判断
                if (!empty($vr['start_time']) && !empty($vr['end_time'])) {

                    $vr['holding_time'] = date('Y-m-d', $vr['start_time']) . '——' . date('Y-m-d', $vr['end_time']);

                } else if(!empty($vr['start_time'] && empty($vr['end_time']))){

                    $vr['holding_time'] = date('Y-m-d', $vr['start_time']) . '——' . '至今';

                }else if(empty($vr['start_time']) && empty($vr['end_time'])){

                    $vr['holding_time'] = '长期';
                }

            }
            $Return['list'] = $list;
        }
        return DataReturn('请求成功', 200,$Return);
    }




    /**
     * @param $stadium_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 场馆地区位置处理
     */
    public static function stadiumLocation($stadium_id)
    {
        $stadiumIfoProvince = '';
        $stadiumIfoCity = '';
        $stadiumIfo = OptionalQuery('stadium', ['id' => $stadium_id]);

        if (is_numeric($stadiumIfo['province'])) {
            $stadiumIfoProvince = place($stadiumIfo['province']);
        }
        if (is_numeric($stadiumIfo['city'])) {
            $stadiumIfoCity = place($stadiumIfo['city']);
        }

        $stadium_address = $stadiumIfoProvince . $stadiumIfoCity . $stadiumIfo['building_name'];

        if($stadiumIfoProvince=='北京' || $stadiumIfoProvince=='上海' || $stadiumIfoProvince=='重庆' || $stadiumIfoProvince=='天津'){

            $stadium_address = $stadiumIfoCity . $stadiumIfo['building_name'];
        }
        return $stadium_address;
    }


    /**
     * @param array $Params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * api 展览详情
     */
    public static function ApiExhibitionInfo(array $Params, $userToken)
    {
        $online = http_type();
        $data = [];
        if (!empty($userToken)) {
            recordHistory($userToken, 'exhibition', $Params['exhibition_id']);
        }
        $field = 'id,title,cover,address,start_time,end_time,stadium_id,subtitle,describe,insideimg_status,insideimg';
        $info = Exhibition::where('id', $Params['exhibition_id'])->field($field)->find();
        if (empty($info)) return DataReturn('该展览不存在!', -1);
        $info['cover'] = $online.$info['cover'];
        $info['insideimg'] = $online.$info['insideimg'];
        $stadiumAddress = self::stadiumLocation($info['stadium_id']);
        $info['location'] = $stadiumAddress;
        //时间判断
        if (!empty($info['start_time']) && !empty($info['end_time'])) {

            $holding_time = date('Y-m-d', $info['start_time']) . '——' . date('Y-m-d', $info['end_time']);

        } else if(!empty($info['start_time'] && empty($info['end_time']))){

            $holding_time = date('Y-m-d', $info['start_time']) . '——' . '至今';

        }else if(empty($info['start_time']) && empty($info['end_time'])){

            $holding_time = '长期';
        }

        $info['holding_time'] = $holding_time;
        //展区数据
        $data['info'] = $info;
        $SelectArea = DisplayModel::where(['exhibition_id' => $info['id'], 'status' => 1])->order('list_order asc,create_time desc')->select()->toArray();
        $append = [];
        foreach ($SelectArea as $key =>&$item){
            //展区图片
            $item['img'] = explode(",",$item['img']);
            foreach ($item['img'] as  &$imgs) {
                $imgs = $online.$imgs;
            }
            if (!empty($item['audio'])) $item['audio'] = $online. $item['audio'];
            //展位数据
            $item['SelectStand'] = DisplaystandModel::where(['area_id' => $item['id']])->order('sort asc,create_time desc')->select()->toArray();
            foreach ($item['SelectStand'] as $k => &$value) {
                $value['images'] = $online . $value['images'];
                if (!empty($value['audio']))  $value['audio'] = $online . $value['audio'];
            }
            if (!empty($item['SelectStand'])){
                $item['copy'] = 'true';
                array_push($append,$item);
                $item['copy'] = 'false';
                $item['title'] = $item['title'].'——'.'展品';
                array_push($append,$item);
            }else{
                array_push($append,$item);
            }
        }
        $data['SelectArea'] = $append;
        return DataReturn('数据获取成功!', 0, $data);
    }



    /*---------------------------------展区 start----------------------------------------*/
    /**
     * @param array $Params
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 展区后台数据列表
     */
    public static function displayList($Params = [])
    {
        $map = [];
        $map['exhibition_id'] = $Params['exhibition_id'];
        $page = isset($Params['page']) ? $Params['page'] : 1;
        $limit = isset($Params['limit']) ? $Params['limit'] : 10;
        if (isset($Params['key']) && !empty($Params['key'])) $map['title'] = ['like', "%" . $Params['key'] . "%"];
        if (isset($Params['start']) && !empty($Params['start'])) $map['create_time'] = ['>= time', $Params['start']];
        if (isset($Params['end']) && !empty($Params['end'])) $map['create_time'] = ['<= time', $Params['end']];
        if (isset($Params['start']) && !empty($Params['start']) && isset($Params['end']) && !empty($Params['end'])) $map['create_time'] = ['between time', [$Params['start'], $Params['end']]];
        $List = DisplayModel::where($map)->page($page, $limit)->order('list_order asc,create_time desc')->select()->toArray();
        $count = DisplayModel::where($map)->count();
        return ['msg' => '', 'code' => 0, 'count' => $count, 'data' => $List];
    }


    /**
     * @param array $Params
     * @return array
     * 展览状态
     */
    public static function displayareaStatus(array $Params = [])
    {
        $query = DisplayModel::where(['id' => $Params['id']])->Update(['status' => $Params['num']]);
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }



    /**
     * @param array $Params
     * @return array
     * 单元格编辑
     */
    public static function displayareaTableEdit(array $Params = [])
    {
        $query = DisplayModel::where(['id' => $Params['id']])->Update([$Params['field'] => $Params['value']]);
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }



    /**
     * @param array $Params
     * @return array
     * 展区编辑
     */
    public static function displayareaEdit(array $Params = [])
    {
        $Data = [
            'title' => $Params['title'],
            'create_time' => time(),
            'img' => $Params['img'] ?? '',
            'audio' => $Params['audio'] ?? '',
            'content' => $Params['content'] ?? ''
        ];
        Db::startTrans();
        try {
            DisplayModel::where(['id' => $Params['id']])->Update($Data);
            Db::commit();
            return DataReturn('操作成功', 200);
        } catch (\Exception $e) {
            Db::rollback();
            return DataReturn($e->getMessage(), 100);
        }
    }




    /**
     * @param array $Params·
     * @return array
     * 展区添加
     */
    public static function displayareaAdd(array $Params = [])
    {
        $Data = [
            'title' => $Params['title'],
            'create_time' => time(),
            'img' => $Params['img'],
            'audio' => $Params['audio'] ?? '',
            'content' => $Params['content'] ?? '',
            'exhibition_id' => $Params['exhibition_id'],
            'list_order'=>self::autoSort($Params['exhibition_id']),
        ];
        if (empty($Data['img'])) {
            return DataReturn('请上传图片', 0);
        }
        Db::startTrans();
        try {
            $Query = DisplayModel::insertGetId($Data);
            if (empty($Query)) {
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
     * @param $exhibition_id
     * @return int
     * 整形
     */
    public static function autoSort($exhibition_id)
    {
        $Max = DisplayModel::where(['exhibition_id'=>$exhibition_id])->max('list_order');
        $newSort = $Max+1;
        return intval($newSort);
    }



    /**
     * @param array $Params
     * @return array
     * 删除
     */
    public static function displayareaDelete(array $Params = [])
    {
        $query = DisplayModel::where(['id' => $Params['id']])->delete();
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }


    /**
     * @param array $Params
     * @return array
     * 批量删除
     */
    public static function displayareaDeleteMore(array $Params = [])
    {
        $where['id'] = ['in', $Params['ids']];
        $query = DisplayModel::where($where)->delete();
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }



    /*---------------------------------展位start------------------------------*/



    /**
     * @param array $Params
     * @return array
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 展位后台数据列表
     */
    public static function displaystandList($Params = [])
    {
        $map = [];
        $map['area_id'] = $Params['area_id'];
        $page = isset($Params['page']) ? $Params['page'] : 1;
        $limit = isset($Params['limit']) ? $Params['limit'] : 10;
        if (isset($Params['key']) && !empty($Params['key'])) $map['area_name'] = ['like', "%" . $Params['key'] . "%"];
        if (isset($Params['start']) && !empty($Params['start'])) $map['create_time'] = ['>= time', $Params['start']];
        if (isset($Params['end']) && !empty($Params['end'])) $map['create_time'] = ['<= time', $Params['end']];
        if (isset($Params['start']) && !empty($Params['start']) && isset($Params['end']) && !empty($Params['end'])) $map['create_time'] = ['between time', [$Params['start'], $Params['end']]];
        $List = DisplaystandModel::where($map)->page($page, $limit)->order('sort asc,create_time desc')->select()->toArray();
        $count = DisplaystandModel::where($map)->count();
        return ['msg' => '', 'code' => 0, 'count' => $count, 'data' => $List];
    }



    /**
     * @param array $Params
     * @return array
     * 展位状态
     */
    public static function displaystandStatus(array $Params = [])
    {
        $query = DisplaystandModel::where(['id' => $Params['id']])->Update(['status' => $Params['num']]);
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }



    /**
     * @param array $Params
     * @return array
     * 单元格编辑
     */
    public static function displaystandTableEdit(array $Params = [])
    {
        $query = DisplaystandModel::where(['id' => $Params['id']])->Update([$Params['field'] => $Params['value']]);
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }



    /**
     * @param array $Params
     * @return array
     * 展位编辑
     */
    public static function displaystandEdit(array $Params = [])
    {
        $Data = [
            'content' => $Params['content'],
            'images' => $Params['images'],
            'audio' => $Params['audio'] ?? '',
            'stand_title'=>$Params['stand_title']??''
        ];
        if (empty($Data['images'])) {
            $Data['images'] = OptionalQuery('display_stand', ['id' => $Params['id']])['images'];
        }
        Db::startTrans();
        try {
            DisplaystandModel::where(['id' => $Params['id']])->Update($Data);
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
     * 展位添加
     */
    public static function displaystandAdd(array $Params = [])
    {
        $Data = [

            'content' => $Params['content'],
            'create_time' => time(),
            'images' => $Params['images'],
            'area_id' => $Params['area_id'],
            'audio' => $Params['audio'] ?? '',
            'stand_title'=>$Params['stand_title']??''
        ];
        if (empty($Data['images'])) {
            return DataReturn('请上传图片', 0);
        }
        Db::startTrans();
        try {
            $Query = DisplaystandModel::insertGetId($Data);
            if (empty($Query)) {
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
    public static function displaystandDelete(array $Params = [])
    {
        $query = DisplaystandModel::where(['id' => $Params['id']])->delete();
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }

    /**
     * @param array $Params
     * @return array
     * 批量删除
     */
    public static function displaystandDeleteMore(array $Params = [])
    {
        $where['id'] = ['in', $Params['ids']];
        $query = DisplaystandModel::where($where)->delete();
        return $query ? DataReturn('操作成功', 200) : DataReturn('操作失败', 100);
    }


}
