<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //配置对应表名
    protected  $table='cart';

    //关闭自动维护时间戳的功能
    public $timestamps=false;
}
