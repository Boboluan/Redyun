<?php
namespace app\api\controller;

use think\Db;

class Index
{
    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 首页推荐数据
     */
    public function IndexResult()
    {
        $data =[];
        $province = input('province') ? input('province') :110000;
        //首页banner
        $IndexImg = Db::name('web_images')->where(['id'=>1])->find();
        $data['banner'] = explode(",",$IndexImg['images']);
        foreach ($data['banner'] as &$item){
            $item = http_type().$item;
        }
        //首页数字联展
        $IndexExhibition = Db::name('exhibition')->field('id,cover,title')->where(['status'=>1,'is_recommend'=>1])->order('sort asc')->limit(6)->select();
        foreach ($IndexExhibition as &$value){
            $value['cover'] = http_type().$value['cover'];
        }
        $data['Exhibition'] = $IndexExhibition;

        //首页视频推荐 数字传播
        $videoField = 'id,title,cover,video';
        $data['video'] = Db::name('video')->where(['is_index'=>1,'status'=>1])->field($videoField)->order('sort asc')->limit(5)->select();
        foreach ($data['video'] as &$v){
            $v['cover'] = http_type().$v['cover'];
            $v['video'] = http_type().$v['video'];
        }

        //首页推荐场馆
        $data['stadium'] = Db::name('stadium')->where(['is_recommend'=>1,'status'=>1,'province'=>$province])->order('sort asc')->field('id,building_name,cover')->limit(8)->select();
        foreach ($data['stadium'] as &$v){
            $v['cover'] = http_type().$v['cover'];
        }

        //首页思政课推荐
        $data['education'] = Db::name('education')->field('id,cover,title,lecturer')->where(['is_recommend'=>1,'status'=>1])->order('sort asc')->limit(5)->select();
        foreach ($data['education'] as &$val){
            $val['cover'] = http_type().$val['cover'];
        }
        //云视听
        $data['cloud'] = Db::name('cloud')->where(['status'=>1])->select();
        foreach ($data['cloud'] as &$cloud){
            $cloud['images'] = http_type().$cloud['images'];
        }
        //首页专题
        $data['article'] = Db::name('article')->where(['is_tui'=>1,'status'=>1])->limit(6)->order('sort asc')->select();
        foreach ($data['article'] as &$art){
            $art['cover'] = http_type().$art['cover'];
        }
        //省份数据
        $data['provinceList'] = ProvinceDataList();
        return ApiReturn(DataReturn('获取数据成功!',0, $data));
    }




    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 首页banner
     */
    public function Banner()
    {
        $data = [];
        return ApiReturn(DataReturn('获取数据成功!',0, $data));
    }


    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 视频推荐
     */
    public function VideoRecommend()
    {
        $data = [];
        $field = 'id,title,cover';
        $data['List'] = Db::name('video')->where(['is_index'=>1,'status'=>1])->field($field)->select();
        foreach ($data['List'] as &$item){
            $item['cover'] = http_type().$item['cover'];
        }
        return ApiReturn(DataReturn('获取数据成功!',0, $data));
    }



    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 思政课推荐
     */
    public function EducationRecommend()
    {
        $data = [];
        $data['List'] = Db::name('eduction')->where(['is_recommend'=>1,'status'=>1])->select();
        foreach ($data['List'] as &$item){
            $item['cover'] = http_type().$item['cover'];
        }
        return ApiReturn(DataReturn('获取数据成功!',0, $data));
    }



    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 云视听
     */
    public function Cloud()
    {
        $data['List'] = Db::name('cloud')->where(['is_index'=>1,'status'=>1])->select();
        foreach ($data['List'] as &$item){
            $item['images'] = http_type().$item['images'];
        }
        return ApiReturn(DataReturn('获取数据成功!',0, $data));
    }





}
