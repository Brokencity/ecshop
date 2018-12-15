<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //配置对应表名
    protected  $table='address';

    //关闭自动维护时间戳的功能
    public $timestamps=false;


}
