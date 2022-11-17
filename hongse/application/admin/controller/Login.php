<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2018/8/2
 * Time: 22:30
 */
namespace app\admin\controller;
use think\Cache;
use think\Controller;
use think\Db;
use org\Verify;
use com\Geetestlib;
use app\models\UserType;
use think\Cookie;
class Login extends Controller
{
    /**
     * 登录页面
     * @return mixed
     */
    public function index()
    {
        return $this->fetch('/login');
    }


    /**
     * 生成验证码
     * @return mixed
     */
    public function checkVerify()
    {
        $config =    [
            'imageH' => 38,// 验证码图片高度
            'imageW' => 120,// 验证码图片宽度
            'codeSet' => '02345689',// 验证码字符集合
            'useZh' => false,//使用中文验证码
            'length' => 4,// 验证码位数
            'useNoise' => true,//是否添加杂点
            'useCurve' => false,//是否画混淆曲线
            'useImgBg' => false,//使用背景图片
            'fontSize' => 16// 验证码字体大小(px)
        ];
        $verify = new Verify($config);
        return $verify->entry();
    }



    /**
     * 验证验证码
     * @return \think\response\Json
     */
    public function doLogin()
    {
        $username = input("param.username");
        $password = input("param.password");
        $verify = new Verify();

        $code = input("param.vercode");
        if (!$verify->check($code)) {
            return json(['code' => -4, 'url' => '', 'msg' => '验证码错误']);
        }
        return  $this->checkAdmin($username,$password);
    }


    /**
     * 验证帐号和密码
     * @param $username
     * @param $password
     * @return \think\response\Json
     */
    public function checkAdmin($username,$password){
        $hasUser = Db::name('admin a')
            ->join('auth_group ag','a.groupid=ag.id','left')
            ->where('username', $username)
            ->field('a.id,a.username,a.password,a.portrait,a.phone,a.loginnum,a.last_login_ip,a.last_login_time,a.real_name,a.status,a.groupid,ag.id agid,ag.title,ag.status ags')
            ->find();
        if(empty($hasUser)){
            return json(['code' => -1, 'url' => '', 'msg' => '管理员不存在']);
        }

        $config = api('Config/lists');
        if($config['web_site_close'] == 0 && $hasUser['id'] !=1 ){
            $this->error('后台已经关闭，请稍后访问');
            return json(['code' => -7, 'url' => '', 'msg' =>'后台已经关闭，请稍后访问']);
        }
        if($config['admin_allow_ip'] && $hasUser['id'] !=1 ){
            if(in_array(request()->ip(),explode(',',$config['admin_allow_ip']))){
                return json(['code' => -8, 'url' => '', 'msg' =>'IP禁止访问']);
            }
        }

        if(md5(md5($password) . config('auth_key')) != $hasUser['password']){
            writelog('管理员【'.$username.'】登录失败：密码错误',100,$hasUser['id'] , $username);
            return json(['code' => -2, 'url' => '', 'msg' => '密码错误']);
        }

        if(1 != $hasUser['status']){
            writelog('管理员【'.$username.'】登录失败：该账号被禁用',100,$hasUser['id'], $username);
            return json(['code' => -5, 'url' => '', 'msg' => '抱歉，该账号被禁用']);
        }
        if($hasUser['ags'] == 2){
            writelog('管理员【'.$username.'】登录失败：'.$hasUser['title'].'身份被禁用',100,$hasUser['id'], $username);
            return json(['code' => -6, 'url' => '', 'msg' =>'抱歉，'.$hasUser['title'].'身份被禁用']);
        }

        if($hasUser['ags'] == null){
            writelog('管理员【'.$username.'】登录失败：所属身份不存在',100,$hasUser['id'],$username);
            return json(['code' => -7, 'url' => '', 'msg' =>'抱歉，所属身份不存在']);
        }

        //获取该管理员的角色信息
        $user = new UserType();
        $info = $user->getRoleInfo($hasUser['groupid']);
        //登录需要存的信息
        $LogInfo = [
            'uid'      =>$hasUser['id'],
            'username' =>$hasUser['username'],
            'phone'    =>$hasUser['phone'],
            'agid'     =>$hasUser['agid'],
            'rolename' =>$info['title'],
            'describe' =>$info['describe'],
            'rule'     => $info['rules'],
            'name'     =>$info['name'],
            'last_time'=>time()
        ];
        $portrait = $hasUser['portrait'];//头像
        $token = CreateAdminToken($hasUser['phone']);//创建token
        Cache::set('portrait',$portrait,86400);
        Cache::set($token,$LogInfo,86400);
        $cookie = new Cookie();
        $cookie->set("token",$token,86400);
        //更新管理员状态
        $param = [
            'loginnum' => $hasUser['loginnum'] + 1,
            'last_login_ip' => request()->ip(),
            'last_login_time' => time()
        ];
        Db::name('admin')->where('id', $hasUser['id'])->update($param);
        writelog('管理员【'.Cache::get('administrator')['username'].'】登录成功',200);
        return json(['code' => 1, 'url' => url('admin/index/index'), 'msg' => '登录成功！,','token'=>$token]);
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        writelog(getAdministrator()['rolename'].'退出登录',200);
        $cookie = new Cookie();
        $token = $cookie->get("token");
        Cache::rm($token);
        Cookie::clear();
        session(null);
        cache('db_config_data',null);
        $this->redirect(url('admin/index/index'));
    }


}
