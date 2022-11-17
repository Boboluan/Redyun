<?php

namespace app\admin\controller;
use app\models\ExhibitionModel;
use think\Cache;
use think\Controller;
use app\models\Node;
use think\Db;
use think\Cookie;
class Base  extends Controller
{
    protected $Administrator;


    public function _initialize()
    {
        $cookie = new Cookie();
        $token = $cookie->get("token");
        $this->Administrator = getAdministrator();
        $this->CheckDisplayStatus();
        if(!Cache::has($token)){
            $this->redirect(url('admin/login/index'));
        }
        if(request()->controller() != 'Data' && request()->action () != 'revert') {
            $adminSta = Db::name ('admin')->where ('id' , $this->Administrator['uid'])->field ('status,username')->find ();
            $roleSta = Db::name ('admin')->alias ('a')->join ('auth_group g' , 'a.groupid=g.id' , 'left')->where ('a.id' , $this->Administrator['uid'])->field ('g.status,g.title')->find ();
            if ( is_null ($adminSta[ 'username' ]) ) {
                writelog ($this->Administrator['username'] . '账号不存在,强制下线！' , 200);
                $this->error ('抱歉，账号不存在,强制下线' , 'admin/login/loginout');
            }
            if ( is_null ($roleSta[ 'title' ]) ) {
                writelog ($this->Administrator['rolename'] . '身份不存在,强制下线！' , 200);
                $this->error ('抱歉，身份不存在,强制下线' , 'admin/login/loginout');
            }
            if ( $adminSta[ 'status' ] == 2 ) {
                writelog ($adminSta[ 'username' ] . '账号被禁用,强制下线！' , 200);
                $this->error ('抱歉，该账号被禁用，强制下线' , 'admin/login/loginout');
            }
            if ( $roleSta[ 'status' ] == 2 ) {
                writelog ($roleSta[ 'title' ] . '角色被禁用,强制下线！' , 200);
                $this->error ('抱歉，该账号角色被禁用，强制下线' , 'admin/login/loginout');
            }
        }
        $auth = new \com\Auth();
        $module     = strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());
        $url        = $module."/".$controller."/".$action;
        //跳过检测以及主页权限
        if($this->Administrator['uid']!=1){
            foreach(config('auth_pass') as $vo){
                $pass[] = strtolower($vo);
            }
            if(!in_array($url,$pass)){
                if(!$auth->check($url,$this->Administrator['uid'])){
                    $this->error('抱歉，您没有操作权限');
                }
            }
        }

        //首页展示用户&菜单信息
        $node = new Node();
        $this->assign([
            'username' => $this->Administrator['username'],
            'portrait' => Cache::get('portrait'),
            'rolename' => $this->Administrator['rolename'],
            'menu' => $node->getMenu($this->Administrator['rule'])
        ]);

        $config = cache('db_config_data');
        if(!$config){
            $config = api('Config/lists');
            cache('db_config_data',$config);
        }
        config($config);
        if(config('web_site_close') == 0 && $this->Administrator['uid'] !=1 ){
            $this->error('站点已经关闭，请稍后访问~');
        }
        if(config('admin_allow_ip') && $this->Administrator['uid'] !=1 ){
            if(in_array(request()->ip(),explode(',',config('admin_allow_ip')))){
                $this->error('403:禁止访问');
            }
        }
    }


    /**
     * place 三级联动
     * @return \think\response\Json
     */
    public function place()
    {
        $area = new \app\common\place\Area;
        $data = $area->areaRegion();
        return json($data);
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
     * 极光推送
     * @param $type 1:推送个人  2:推送全体
     * @param $alias 别名 user_id OR token
     * @param $message 推送消息内容
     * @param $extras 扩展字段接受数组
     * @return array
     */
    public function Jpush($type,$alias,$message,$extras)
    {
        $alias = (string)$alias;
        import('jpush.autoload', EXTEND_PATH);
        //初始化JPushClient
        $client = new \JPush\Client(config('jpush.appKey'),config('jpush.masterSecret'));
        //生成推送Payload构建器
        $push = $client->push();
        //推送平台 'all'  OR  ['ios','android']  OR  'ios','android'
        $push->setPlatform('all');
        //1:推送个人  2:推送全体
        if($type==1){
            $push->addAlias($alias);//按别名推送
        }else{
            $push->addAllAudience();//广播消息推送
        }
        //IOS推送
        $push->iosNotification($message, [
                'alert'=>$message,
                'badge' => '+1',
                'extras' => $extras,
                'sound'=>'default'
            ]
        );
        //Android推送
        $push->androidNotification($message, [
                'alert'=>$message,
                'extras' => $extras
            ]
        );
        return $push->send();
    }


}
