<?php

namespace app\models;
use think\Model;
use think\Db;

class ArticleModel extends Model
{
    protected $name = 'article';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    protected $dateFormat = 'Y-m-d';

}
