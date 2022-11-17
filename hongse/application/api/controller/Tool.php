<?php


namespace app\api\controller;


class Tool extends Common
{


    /**
     *  查找匹配文件夹
     *  返回结果后拼接路径
     *  不指定下属子目录
     */
    public function ImgList()
    {
        $basePath = './uploads/images/';
        $dirs = array_map('basename',glob($basePath.DIRECTORY_SEPARATOR.'*',GLOB_ONLYDIR));
        foreach ($dirs as $vr)
        {
            $dirPath = $basePath.$vr;
            $imgList = json_decode($this->getDir($dirPath),true);
            foreach ($imgList as $item)
            {
                if(filesize($item)>=5000000)
                {
                    $this->image_png_size_add($item,$item);
                }
            }
        }
        return 'success';
    }


    /**
     *  查找匹配文件夹
     *  返回结果后拼接路径
     *  指定目录
     */
    public function ImgListcatalog()
    {
        $dirName = input('dirname');
        if(empty($dirName)) return '文件名必须';
        $dirPath = './uploads/images/'.trim($dirName);
        $imgList = json_decode($this->getDir($dirPath),true);
        foreach ($imgList as $item)
        {
            if(filesize($item)>=5000000)
            {
                $this->image_png_size_add($item,$item);
            }
        }
        return 'success';
    }




    private function getDir($dirPath)
    {
        $List = [];
        $handle = opendir($dirPath);
        $i = 0;
        while (false !== ($file = readdir($handle))){
            list($filesName,$suffix) = explode(".",$file);
            if($suffix=="png" or $suffix=="jpg" or $suffix=="JPG"){
                if (!is_dir('./'.$file)) {
                    $List[$i] = $dirPath.'/'.$file;
                    $i++;
                }
            }
        }
        return json_encode($List);
    }



    private function image_png_size_add($imgsrc,$imgdst){
        set_time_limit(0);
        ini_set('memory_limit','2048');
        ini_set('max_execution_time',0);
        list($width,$height,$type) = getimagesize($imgsrc);
        $new_width = $width;
        $new_height =$height;
        switch($type){
            case 1:
                break;
            case 2:
                header('Content-Type:image/jpeg');
                $image_wp = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_wp, $imgdst,config('quality'));
                imagedestroy($image_wp);
                break;
            case 3:
                header('Content-Type:image/png');
                $image_wp=imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefrompng($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_wp, $imgdst,config('quality'));
                imagedestroy($image_wp);
                break;
        }
    }


}
