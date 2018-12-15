<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    //设置对应表名
    protected $table='goods';
    //关闭自动维护时间戳的功能
    public $timestamps=false;
}
