<?php

namespace app\admin\controller;
use think\Cache;
use think\Controller;
use think\File;
use think\Request;
use think\Db;
use org\Qiniu;
use org\Upayun;
class Upload extends Base
{
    /**
     * 上传图片到又拍云
     * @throws \Exception
     */
    public function uploadOnYpy(){
        $filePath = $_FILES['file']['tmp_name'];
        //取出图片后缀
        $type = explode(".",$_FILES['file']['name']);
        $type = end($type);
        //组装图片名
        $key = md5(time().uuid()).'.'.$type;
        $up = new Upayun();
        $data = $up->uploadFile($filePath,$key,'/up/');
        echo $data;
    }

    /**
     * 删除又拍云图片文件
     * @return
     */
    public function deleteYpy(){
        $add = input('add');
        $up = new Upayun();
        $res = $up->delFile($add);
        if($res){
            return json(['code'=>200,'msg'=>'删除成功！']);
        }else{
            return json(['code'=>100,'msg'=>'删除失败！']);
        }
    }


    /**
     * deleteImg 删除七牛图片文件
     * @return
     */
    public function deleteImg(){
        $add = input('add');
        $up = new Qiniu();
        $res = $up->delFile($add,'kevin');
        if($res){
            return json(['code'=>100,'msg'=>'删除失败！']);
        }else{
            return json(['code'=>200,'msg'=>'删除成功！']);
        }
    }


    /**
     * upload 上传图片到七牛云
     * @throws \Exception
     */
    public function upload(){
        $filePath = $_FILES['file']['tmp_name'];
        //取出图片后缀
        $type = explode(".",$_FILES['file']['name']);
        $type = end($type);
        //组装图片名
        $key = md5(time().uuid()).'.'.$type;
        $up = new Qiniu();
        $data = $up->uploadFile($filePath,$key);
        echo $data;
    }


    /**
     *
     * 上传音频
     */
    public function uploadAudio()
    {
        $file = request()->file('file');
        $info  = $file->getInfo();
        $type = explode(".",$info['name']);
        $type = end($type);
        $key = md5(time().uuid()).'.'.$type;
        $dir = 'uploads/audio/'.date('Y-m-d',time());
        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        $path = $file->move($dir,$key);
        if($path){
            $filePath = $path->getPathname();
            $filePath = '/'.$filePath;
            return  $filePath;
        }else{
            return ApiReturn(DataReturn('上传失败'));
        }
    }


    /*
     * layui上传图片&音频
     */
    public function layUpload(){
        set_time_limit (0);
        $filePath = $_FILES['file']['tmp_name'];
        //取出图片后缀
        $type = explode(".",$_FILES['file']['name']);
        $type = end($type);
        //组装图片名
        $key = md5(time().uuid()).'.'.$type;
        $up = new Qiniu();
        $data = $up->uploadFile($filePath,$key);
        return json(['code'=>0,'msg'=>'','data'=>['src'=>config('qiniu.domain').$data]]);
    }

    /*
     * 上传视频
     */
    public function layUploadVideo(){
        set_time_limit (0);
        $filePath = $_FILES['file']['tmp_name'];
        //取出文件后缀
        $type = explode(".",$_FILES['file']['name']);
        $type = end($type);
        //组装文件名
        $key = md5(time().uuid()).'.'.$type;
        $up = new Qiniu();
        $data = $up->uploadVideo($filePath,$key);
        echo $data;
    }


    /*
     * wangEditor图片上传
     */
    public function wangUpload(){
        foreach($_FILES as $key=>$vo){
            $filePath = $vo['tmp_name'];
            //取出图片后缀
            $type = explode(".",$vo['name']);
            $type = end($type);
            //组装图片名
            $key = md5(time().uuid()).'.'.$type;
            $up = new Qiniu();
            $name = $up->uploadFile($filePath,$key);
            $data[] = config('qiniu.domain').$name;
        }
        return json(['errno'=>0,'data'=>$data]);
    }


    /**
     * 百度富文本上传图片至第三方CDN接口
     * @throws \Exception
     */
    public function ueditorUpload(){
        $file = request()->file('upfile');
        $info  = $file->getInfo();
        //取出图片后缀
        $type = explode(".",$info['name']);
        $type = end($type);
        //组装图片名
        $key = md5(time().uuid()).'.'.$type;
        $up = new Qiniu();
        $data = $up->uploadFile($info['tmp_name'],$key);
        //百度富文本上传文件到CDN upFile
        $res = array(
            "state"    => "SUCCESS",          //上传状态，上传成功时必须返回"SUCCESS"
            "url"      => config('qiniu.domain').$data,            //CDN地址
            "title"    => $key,          //新文件名
            "original" => $info['tmp_name'],       //原始文件名
            "type"     => ".".$type,           //文件类型
            "size"     => $info['size'],           //文件大小
        );
        echo json_encode($res);
    }



    /**
     * uploadLocality图片上传至本地&压缩
     */
    public function uploadLocality(){
        $file = request()->file('file');
        $fileInfo = $file->getInfo();
        $dir = ROOT_PATH . 'public' . DS . 'uploads/images';
        if(!file_exists($dir)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($dir, 0700,true);
        }
        $info = $file->move($dir);
        if($info){
            $newName = $info->getSaveName();
            if($fileInfo['size']>5000000){
                image_png_size_add(ROOT_PATH . 'public' . DS . 'uploads/images/'.$newName,ROOT_PATH . 'public' . DS . 'uploads/images/'.$newName);
            }
            $path = "/uploads/images/{$newName}";
            return ApiReturn(DataReturn('上传成功!',200,$path));
        }else{
            return ApiReturn(DataReturn($file->getError(),-1));
        }
    }




    /**
     * uploadLocality图片上传至本地&压缩(多图)
     */
    public function uploadLocalityMore(){
        $file = request()->file('file');
        $fileInfo = $file->getInfo();
        $dir = ROOT_PATH . 'public' . DS . 'uploads/images';
        if(!file_exists($dir)){
            mkdir($dir, 0700,true);
        }
        $info = $file->move($dir);
        if($info){
            $newName = $info->getSaveName();
            if($fileInfo['size']>=5000000){
                image_png_size_add(ROOT_PATH . 'public' . DS . 'uploads/images/'.$newName,ROOT_PATH . 'public' . DS . 'uploads/images/'.$newName);
            }
            $path = "/uploads/images/{$newName}";
            echo $path;
        }else{
            return ApiReturn(DataReturn($file->getError(),-1));
        }
    }


    /**
     * deleteLocality 删除本地图片
     * @return \think\response\Json
     */
    public function deleteLocality(){
        $add  = input('add');
        $add = substr ($add,1);
        if(unlink($add)){
            return json(['code'=>200,'msg'=>'删除成功！']);
        }else{
            return json(['code'=>100,'msg'=>'删除失败！']);
        }
    }



    /**
     *视频上传
     * @param $files
     * @param string $path
     * @param array $imagesExt
     * @return string
     */
    function upload_video($imagesExt=['mp4'])
    {
        @set_time_limit(0);
        ini_set('max_execution_time',0);
        ini_set('memory_limit', '1024M');
        $file = request()->file('file');
        $info = $file->getInfo();
        $type = explode(".",$info['name']);
        $type = end($type);
        $dirPath = 'uploads/video/'.date('Ymd');
        // 判断文件类型
        if (!in_array($type,$imagesExt)){
            return 1000;//非法文件类型
        }
        // 判断是否存在上传到的目录
        if (!is_dir($dirPath)){
            mkdir($dirPath,0777,true);
        }
        // 生成唯一的文件名
        $fileName = md5(uniqid(microtime(true),true)).'.'.$type;
        // 将文件名拼接到指定的目录下
        $path = $file->move($dirPath,$fileName);
        if($path){
            $filePath = $path->getPathname();
            $filePath = '/'.$filePath;
            return  $filePath;
        }else{
            return '上传失败';
        }
    }




    /**
     * 视频上传
     */
    public function upload_video22(){
        $video = $_FILES['file'];
        $dirPath = 'uploads/video/'.date('Ymd');
        $res = $this->upload_file($video,$dirPath);
        return http_type().'/'.$res;
    }


    /**
     *视频上传
     * @param $files
     * @param string $path
     * @param array $imagesExt
     * @return string
     */
    public function upload_file($files, $path,$imagesExt=['mp4'])
    {
        // 判断错误号
        if ($files['error'] == 00) {
            $ext = strtolower(pathinfo($files['name'],PATHINFO_EXTENSION));
            // 判断文件类型
            if (!in_array($ext,$imagesExt)){
                return 1000;//非法文件类型
            }
            // 判断是否存在上传到的目录
            if (!is_dir($path)){
                mkdir($path,0777,true);
            }
            // 生成唯一的文件名
            $fileName  = md5(uniqid(microtime(true),true)).'.'.$ext;
            // 将文件名拼接到指定的目录下
            $destName = $path."/".$fileName;
            // 进行文件移动
            if (!move_uploaded_file($files['tmp_name'],$destName)){
                return 1001;//文件上传失败
            }
            return $destName;//上传成功，返回上传路径
        } else {
            // 根据错误号返回提示信息
            switch ($files['error']) {
                case 1:
                    echo 2000;//上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值
                    break;
                case 2:
                    echo 2001;//上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值
                    break;
                case 3:
                    echo 2002;//文件只有部分被上传
                    break;
                case 4:
                    echo 2003;//没有文件被上传
                    break;
                case 6:
                    echo 2004;//找不到临时文件夹
                    break;
                case 7:
                    echo 2005;//文件写入错误
                    break;
            }
        }
    }


    /**
     * video 视频文件上传至本地
     */
    public function video(){
        @set_time_limit(5 * 60);
        ini_set('memory_limit', '1024M');
        $targetDir = ROOT_PATH . 'public' . DS . 'uploads/video_tmp';
        $uploadDir = ROOT_PATH . 'public' . DS . 'uploads/video/'.date('Ymd');
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // Create target dir
        if (!file_exists($targetDir)) {
            @mkdir($targetDir,0700,true);
        }

        // Create target dir
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir,0700,true);
        }

        // Get a file name
        if (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        $filePath = $targetDir . DS . iconv("UTF-8","gb2312",$fileName);
//        $uploadPath = $uploadDir . DS . iconv("UTF-8","gb2312",$fileName);

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DS . $file;

                // If temp file is current file proceed to the next
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

// Open temp file
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
            }
            // Read binary input stream and append it to temp file
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        if ($done) {
            $name = uuid();
            if (!file_exists($uploadDir . DS . $name)) {
                @mkdir($uploadDir . DS . $name,0700,true);
            }
            $uploadPath = $uploadDir . DS . $name . DS . iconv("UTF-8","gb2312",$fileName);
            if (!$out = @fopen($uploadPath, "wb")) {
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }
            if (flock($out, LOCK_EX)) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }

                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }

                flock($out, LOCK_UN);
            }
            @fclose($out);
            $path = '/uploads/video/'.date('Ymd'). '/' .$name. '/' .$fileName;
            return $path;
        }
    }






    //后台用户修改头像上传
    public function updateFace(){
        $base64url = input('base64url');
        $arr = base64_img($base64url,true);
        if($arr['code'] == 200){
            $res = Db::name('admin')->where('id',input('id'))->update(["portrait"=>$arr['msg']]);
            if($res){
                Cache::set('portrait', $arr['msg'],0); //用户头像
                return json(['code'=>200,'msg'=>"上传成功"]);
            }else{
                return json(['code'=>100,'msg'=>"上传失败"]);
            }
        }elseif($arr['code'] == 100){
            writelog('管理员上传头像失败',100);
            return json($arr);
        }
    }

    //多图修改测试页面
    public function showImg(){
        $photo = Db::name('img')->where('id',1)->value('img');
        $arr = explode(',',$photo);
        $this->assign ('photo',$photo);
        $this->assign ('arr',$arr);
        return $this->fetch('/webuploader2');
    }
}
