<?php

namespace app\api\controller;

use app\models\ExhibitionModel;
use think\cache\driver\Redis;
use think\Controller;
use think\Db;
use think\Request;

class Common extends controller
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        $this->CheckLogin();

        $this->CheckDisplayStatus();
    }


    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 变更展览状态
     */
    public function CheckDisplayStatus()
    {
        $list = ExhibitionModel::select()->toArray();
        foreach ($list as $datum){
            //变更往期陈列
            if (is_numeric($datum['end_time']) && $datum['end_time'] < time()) {
                ExhibitionModel::where(['id'=>$datum['id']])->update(['display_status'=>0]);
            }elseif (!is_numeric($datum['end_time']) && $datum['end_time']=='至今'){
                ExhibitionModel::where(['id'=>$datum['id']])->update(['display_status'=>1]);
            }
        }
    }


    /**
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * 登录检测
     */
    public function CheckLogin()
    {
        $token = request()->header('token');
        if(!empty($token)){
            $Verify = checkToken($token);
            if($Verify['code']!=0){
                return DataReturn('',$Verify['code']);
            }else{
                $user_token = $Verify['data']['user_token'];
                self::ClearBrowsingHistory($user_token);
                return DataReturn('token验证成功',0,$user_token);
            }
        }
        return DataReturn('',-1000);
    }



    /**
     * @param $token
     * @return array
     * 验证用户token
     */
    public static function CheckUserStatus($token)
    {
        if(!empty($token)){
            $Verify = checkToken($token);
            if($Verify['code']!=2000){
                return DataReturn('',-1000);
            }else{
                $user_token = $Verify['data']['user_token'];
                self::ClearBrowsingHistory($user_token);
                return DataReturn('token验证成功',0,$user_token);
            }
        }
        return DataReturn('',-1);
    }






    /**
     * @param $user_token
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * 删除过期的浏览器记录
     */
    public static function ClearBrowsingHistory($user_token)
    {
        $ClearTime = 86400*7;
        $Data = Db::name('brow_history')->where(['user_token'=>$user_token])->select();//读取该用户缓存
        if(!empty($Data)){
            foreach ($Data as &$item){
                if((time() - (int)$item['add_time'])>$ClearTime){
                    Db::name('brow_history')->where(['user_token'=>$user_token,'id'=>$item['id']])->delete();
                }
            }
        }
    }




}
