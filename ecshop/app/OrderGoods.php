<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    //配置对应的表名
    protected $table = 'order_goods';

    //关闭自动维护时间戳的功能
    public $timestamps=false;
}
