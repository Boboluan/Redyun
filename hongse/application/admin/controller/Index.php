<?php
namespace app\admin\controller;
use app\models\ArticleModel;
use app\models\CustomModel;
use app\models\EducationModel;
use app\models\StadiumModel;
use app\models\UsercodeModel;
use app\models\VideoModel;
class Index extends  Base
{
    /**
     * 框架页面
     * @return mixed
     */
    public function index()
    {
        return $this->fetch('/index');
    }

    /**
     * 首页展示main
     * @return mixed|\think\response\Json
     */
    public function indexPage()
    {
        //数据统计
        $admin = $this->Administrator['username'];
        $ArticleCount = ArticleModel::count();
        $userNum = UsercodeModel::count();
        $message = CustomModel::count();
        $venueCount = StadiumModel::count();
        $video = VideoModel::count();
        $education =EducationModel::count();
        return $this->fetch('index/index',[
            'admin'=>$admin,
            'article'=>$ArticleCount,
            'userNum'=>$userNum,
            'message'=>$message,
            'venue'=>$venueCount,
            'video'=> $video,
            'education'=>$education
        ]);
    }

    /**
     * 清除缓存
     */
    public function clear() {
        if (delete_dir_file(TEMP_PATH)) {
            writelog('清除缓存成功',200);
            return json(['code' => 200, 'msg' => '清除缓存成功']);
        } else {
            writelog('清除缓存失败',100);
            return json(['code' => 100, 'msg' => '清除缓存失败']);
        }
    }


    public function webuploader()
    {
        return $this->fetch('/webuploader');
    }


}
