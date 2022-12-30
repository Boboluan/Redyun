<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class MatterCount extends Authenticatable
{

    protected $table = 'matter_count';

    public $timestamps = false;//不需要自动更新时间字段

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [//允许入库的字段
        'matter_id',
        'search_time',
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
        'search_time'=>'datetime',
    ];
}
