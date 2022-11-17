<?php

namespace app\models;

use  think\Model;

class EducationCategoryModel extends Model
{

    protected $name = 'education_cate';

    public static function VideoCateList()
    {
        $List = self::select()->toArray();
        return $List;
    }


    public static function SelectCate()
    {
        $List = self::where(['status'=>1])->select();
        return $List;
    }


}
