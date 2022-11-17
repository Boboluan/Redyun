<?php

namespace app\api\controller;

class Upload extends Common
{

    /**
     * @return \think\response\Json
     *  抽取公共上传文件
     */
    function apiuploads(){
        $file = request()->file('file');
        if(empty($file)){
            return ApiReturn(DataReturn('请上传图片',-1));
        }
        $dir = ROOT_PATH . 'public' . DS . 'uploads/images';
        if(!file_exists($dir)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($dir, 0700,true);
        }
        $info = $file->move($dir);
        if($info){
            $newName = $info->getSaveName();
            $path = http_type()."/uploads/images/{$newName}";
            return ApiReturn(DataReturn('上传成功!',0,$path));
        }else{
            return ApiReturn(DataReturn($file->getError(),-1));
        }
    }



    /**
     * @return \think\response\Json
     *  抽取公共上传文件
     */
    public function apiuploads2(){
        $file = request()->file('file');
        if(empty($file)){
            return ApiReturn(DataReturn('请上传图片',-1));
        }
        $dir = '/uploads/images/'.date('Y-m-d',time());
        if(!file_exists($dir)){
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($dir, 0700,true);
        }
        $info = $file->move($dir);
        if($info){
            $newName = $info->getSaveName();
//            $path = http_type()."/uploads/images/{$newName}";
            $path = http_type().$dir.$newName;
            return ApiReturn(DataReturn('上传成功!',0,$path));
        }else{

            return ApiReturn(DataReturn($file->getError(),-1));
        }
    }


}
