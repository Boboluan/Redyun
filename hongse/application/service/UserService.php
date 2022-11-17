<?php

namespace app\service;

use app\models\ApiuserModel;
use app\models\UserCollectModel;
use think\Cache;
use think\Db;
use think\Exception;
use think\Loader;

class UserService
{

    /**
     * @param array $params
     * @return
     * 用户登录
     */
    public static function Login($params = [])
    {
        $username  = trim($params['username']);
        $password  = trim($params['password']);
        $Info = ApiuserModel::where(['username'=>$username])->find();
        $validate = Loader::validate('Uservaildate');
        if(!$validate->scene('login')->check($params)){
            return DataReturn($validate->getError(),-1);
        }
        if(empty($Info)){
            return DataReturn('该用户不存在',-1);
        }

        if($Info['status']!=1){
            return DataReturn('该用户暂时无法登陆',-1);
        }

        if(md5($password.'revolution') != $Info['password']){
            return DataReturn('密码错误',-1);
        }else{
            $token = CreateToken($Info['user_token']);
            return DataReturn('登录成功',0,['token'=>$token,'userinfo'=>$Info]);
        }

    }


    /**
     * @param array $params
     * @return
     * 注册
     */
    public static function Register($params = [])
    {
        $password   = trim($params['password']);
        $phone      = trim($params['phone']);
        $verifCode  = trim($params['verifCode']);
        $userInfo = ApiuserModel::where(['phone' => $phone])->find();
        $Info = empty($userInfo) ? array():$userInfo->toArray();
        //验证验证码时效性
        $key = $phone.'register';
        $code = Cache::get($key);
        $validate = Loader::validate('Uservaildate');
        if(!$validate->scene('register')->check($params)){
            return DataReturn($validate->getError(),-1);
        }
        if(empty($code)){
            return DataReturn('验证码已失效请重新获取',-1);
        }
        if (!empty($Info)) {
            return DataReturn('该用户已被注册',-1);
        }
        if ($verifCode != $code) {
            return DataReturn('验证码不正确',-1);
        }
        $UserToken = self::UserToken($phone);
        $RegisterArr = [
            'headimg'=> http_type().'/uploads/images/20220317/head.png',
            'nickname'=>'用户'.mt_rand(1000,9999),
            'username'=>$phone,
            'password'=> md5($password.'revolution'),
            'phone'   => $phone,
            'status'  => 1,
            'create_time'=>time(),
            'user_token'=>$UserToken,
            'address'  => '暂无'
        ];
        DB::startTrans();//开启事务
        try {
            $Userid = ApiuserModel::insertGetId($RegisterArr);
            if (!$Userid) {
                throw new \Exception('error');
            }
            DB::commit();
            Cache::rm($key);//删除缓存
            $token = CreateToken($UserToken);
            $userInfo = ApiuserModel::where(['id'=>$Userid])->find();
            return DataReturn('注册成功',0,['token'=>$token,'userInfo'=>$userInfo]);
        } catch (\Exception $e) {
            DB::rollback();
            return DataReturn($e->getMessage(),$e->getCode());
        }

    }



    /**
     * @param $phone
     * @return string
     * 加密生成token算法
     */
    public static function UserToken($phone)
    {
        $str = md5(uniqid(md5(microtime(true)),true));
        $token = sha1($str.$phone);
        return $token;
    }


    /**
     * @param array $Params
     * @param $token
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 用户收藏列表
     */
    public static function Collect($Params,$userToken)
    {
        $Return = [];
        $page  = isset($Params['page'])?  $Params['page']:1;
        $limit = isset($Params['limit'])? $Params['limit']:9;
        $where['module'] = trim($Params['module']);
        $where['user_token'] = trim($userToken);
        $List = UserCollectModel::where($where)->page($page,$limit)->select()->toArray();
        switch($Params['module']){

            case 'venue': //革命场馆
                $table = 'stadium';
                $field = 'id,building_name,cover';
                break;

            case 'digitalvenue': //数字传播
                $table = 'video';
                $field = 'id,title,cover';
                break;

            case 'education': //思政课
                $table = 'education_cate';//系列
                $field = 'id,title,cover';
                break;

            case 'exhibition': //数字联展
                $table = 'exhibition';
                $field = 'id,title,cover';
                break;
            default:
                return DataReturn('未知模块',-1);
        }
        if(!empty($List)){
            foreach($List as &$value){
                $Collect[] = Db::name($table)->field($field)->where(['id'=>$value['product_id']])->find();
            }

            foreach ($Collect as &$item){
                $item['cover'] = http_type().$item['cover'];
            }
            $Return['data'] = $Collect;
            $Return['totalNum'] = UserCollectModel::where(['user_token'=>$userToken,'module'=>$Params['module']])->count();
            $Return['pageSize'] = ceil($Return['totalNum']/$limit);
            $Return['nowPage'] = $page;
        }else{
            $Return = [];
        }
        return DataReturn('获取数据成功',0,$Return);
    }




    /**
     * @param array $Params
     * @param $token
     * @return array
     * 用户添加收藏
     */
    public static function AddCollect(array $Params,$userToken)
    {
        DB::startTrans();
        try {
            $Collect = [
                'product_id' =>$Params['id'],
                'module'     =>$Params['module'],
                'add_time'   =>time(),
                'user_token' =>$userToken,
            ];
            $Query = UserCollectModel::insert($Collect);
            if(!$Query){throw new Exception('the sql query error');}
            Db::commit();
            return  DataReturn('收藏成功',0);
        }catch(Exception $e){
            Db::rollback();
            return  DataReturn($e->getMessage(),-1);
        }
    }



    /**
     * @param array $Params
     * @param $token
     * @return array
     * 用户删除收藏
     */
    public static function DelCollect(array $Params,$userToken)
    {
        DB::startTrans();
        try {
            $Where = [
                'product_id' =>$Params['id'],
                'module'     =>$Params['module'],
                'user_token' =>$userToken,
            ];
            $Query = UserCollectModel::Where($Where)->delete();
            if(!$Query){throw new Exception('the sql query error');}
            Db::commit();
            return  DataReturn('取消成功',0);
        }catch(Exception $e){
            Db::rollback();
            return  DataReturn($e->getMessage(),-1);
        }
    }



    /**
     * @param array $Params
     * @param $token
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 用户足迹列表
     */
    public static function HistoryList(array $Params,$userToken)
    {
        $data = [];
        $where = [];
        $page  = isset($Params['page']) ? $Params['page']:1;
        $limit = isset($Params['limit'])? $Params['limit']:9;
        switch($Params['module']){

            case 'venue': //革命场馆
                $table = 'stadium';
                $field = 'id,cover,building_name';
                break;

            case 'digitalvenue': //数字传播
                $table = 'video';
                $field = 'id,cover,title';
                break;

            case 'education': //思政课
                $table = 'education';
                $field = '';
                break;

            case 'exhibition': //数字联展
                $table = 'exhibition';
                $field = '';
                break;

            default:
                return DataReturn('未知模块',-1);
        }
        $where['user_token'] = $userToken;
        $where['module'] = $Params['module'];
        $Data = Db::name('brow_history')->where($where)->page($page,$limit)->select();
        if(!empty($Data)){
            foreach($Data as $value){
                $data['HistoryList'][] = Db::name($table)->field($field)->where(['id'=>$value['product_id']])->find();
            }
            foreach ($data['HistoryList'] as &$item){
                $item['cover'] = http_type().$item['cover'];
            }
            $totalNum = Db::name('brow_history')->where($where)->count();
            $data['pageSize'] = ceil($totalNum/$limit);
            $data['totalNum'] = $totalNum;
            $data['NowPage'] = $page;
        }else{
            $data = [];
        }
        return DataReturn('获取数据成功',0,$data);
    }




    /**
     * @param array $Params
     * @param $token
     * @return array
     * 用户足迹--记录
     */
    public static function AddHistory($Params = [],$userToken)
    {
        DB::startTrans();
        try {
            $History = [
                'product_id' =>$Params['id'],
                'module'     =>$Params['module'],
                'add_time'   =>time(),
                'user_token' =>$userToken,
            ];
            $Query = Db::name('brow_history')->insert($History);
            if(!$Query){throw new Exception('the sql query error');}
            Db::commit();
            return  DataReturn('记录成功',0);
        }catch(Exception $e){
            Db::rollback();
            return  DataReturn($e->getMessage(),-1);
        }
    }



    /**
     * @param array $params
     * @return
     * 修改资料
     */
    public static function InfoEdit($params = [],$userToken)
    {
        $account = [
            'nickname'  => trim($params['nickname']),
            'phone'     => trim($params['phone']),
            'province'  => trim($params['province']),
            'city'      => trim($params['city']),
            'headimg'   => trim($params['headimg']),
            'address'   => trim($params['address']),
        ];
        $validate = Loader::validate('Uservaildate')->scene('updateUserinfo');
        if(!$validate->check($account)){
            return DataReturn($validate->getError(),-1);
        }
        DB::startTrans();//开启事务
        try {
            $Save = ApiuserModel::where(['user_token'=>$userToken])->Update($account);
            if (!$Save) {
                throw new \Exception('请勿提交重复资料');
            }
            DB::commit();
            return DataReturn('修改成功', 0,['userinfo'=>self::UserInfo($userToken)['data']]);
        } catch (\Exception $e){
            DB::rollback();
            return DataReturn($e->getMessage(),-1);
        }

    }



    /**
     * @return
     * 当前信息
     */
    public static function UserInfo($userToken)
    {
        $Info = ApiuserModel::where(['user_token'=>$userToken])->find()->toArray();
        return DataReturn('获取成功',0,$Info);
    }





    /**
     * @param array $params
     * @return
     * 修改重置密码
     */
    public static function accountService(array $params,$userToken)
    {
        $account = [
            'phone'=>trim($params['phone']),
            'newpassword'=>trim($params['password']),
            'setPasscode'=>trim($params['setPasscode']),
            'reqirPass' =>trim($params['reqirPass'])
        ];
        $key = $account['phone'].'setPass';
        $code = Cache::get($key);
        $validate = Loader::validate('Uservaildate');
        if(!$validate->scene('setPass')->check($account)){
            return DataReturn($validate->getError(),-1);
        }
        if(empty($code)){
            return DataReturn('验证码已失效,请重新获取',-1);
        }

        if($account['setPasscode'] != $code){
            return DataReturn('验证码错误',-1);
        }
        DB::startTrans();
        try {
            $password = trim($account['newpassword'].'revolution');
            ApiuserModel::where(['user_token'=>$userToken])->Update(['password'=>md5($password)]);
            Cache::rm($key);
            DB::commit();
            return DataReturn('重置成功',0);
        }catch(\Exception $e){
            DB::rollback();
            return DataReturn('重置失败',-1);
        }
    }




    /**
     * @param array $params
     * @return
     * 忘记密码
     */
    public static function ForgetService($params = [])
    {
        $account = [
            'phone'=>trim($params['phone']),
            'newpassword'=>trim($params['password']),
            'setPasscode'=>trim($params['setPasscode']),
            'reqirPass' =>trim($params['reqirPass'])
        ];
        $key = $account['phone'].'setPass';
        $code = Cache::get($key);
        $validate = Loader::validate('Uservaildate');
        if(!$validate->scene('setPass')->check($account)){
            return DataReturn($validate->getError(),-100);
        }
        if(empty($code)){
            return DataReturn('验证码已失效,请重新获取',-1);
        }
        if($account['setPasscode'] != $code){
            return DataReturn('验证码错误',-1);
        }
        DB::startTrans();
        try {
            $password = trim($account['newpassword'].'revolution');
            ApiuserModel::where(['phone'=>$account['phone']])->Update(['password'=>md5($password)]);
            Cache::rm($key);
            DB::commit();
            return DataReturn('修改成功',0);
        }catch(\Exception $e){
            DB::rollback();
            return DataReturn('修改失败',-1);
        }
    }


}
