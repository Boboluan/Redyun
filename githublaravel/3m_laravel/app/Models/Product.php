<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Product extends Authenticatable
{

    protected $table = 'product';

    public $timestamps = false;//不需要自动更新时间字段

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [//允许入库的字段
        'name',
        'chinese_name',
        'cas',
        'english_name',
        'mac',
        'twa',
        'stel',
        'remark',
        'recommend',
        'masktype_one',
        'facemasktype_one',
        'filterbox_one',
        'filtercover_one',
        'cottonfilter_one',
        'facemasktype_two',
        'filterbox_two',
        'filtercover_two',
        'cottonfilter_two',
        'facemasktype_three',
        'filterbox_three',
        'filtercover_three',
        'cottonfilter_three',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'create_time'=>'datetime',
    ];

    public static function insert(array $params)
    {
    }
}
