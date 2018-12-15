<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Protocol extends Model
{
    //配置对应的表名
    protected $table = 'protocol';

    //关闭自动维护时间戳的功能
    public $timestamps=false;
}
