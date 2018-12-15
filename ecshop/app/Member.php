<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    //配置对应表名
    protected  $table='member';

    //关闭自动维护时间戳的功能
    public $timestamps=false;
}
