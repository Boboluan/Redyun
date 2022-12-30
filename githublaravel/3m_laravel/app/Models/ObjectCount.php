<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ObjectCount extends Authenticatable
{

    protected $table = 'object_count';

    public $timestamps = false;//不需要自动更新时间字段

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [//允许入库的字段
       'id',
        'object_type',
        'object_name',
        'object_id',
        'action',
        'user_token',
        'create_time'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'object_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [

    ];
}
