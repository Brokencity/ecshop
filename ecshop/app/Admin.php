<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    //配置对应表名
    protected  $table='admin';

    //关闭自动维护时间戳的功能
    public $timestamps=false;
}