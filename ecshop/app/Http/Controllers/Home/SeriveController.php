<?php

namespace App\Http\Controllers\Home;

use App\category;
use App\Goods;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use function Sodium\compare;

class SeriveController extends Controller
{

    //帮助中心
    public function Forever()
    {
        //dd(request());
        return view('Home.series.Forever');
    }

    //帮助中心
    public function help_b()
    {
        //dd(request());
        return view('Home.help.help_b');
    }

    //帮助中心
    public function help_c()
    {
        //dd(request());
        return view('Home.help.help_c');
    }


    /*    //查找数据库
        public function list_all(){
            //接受数据
           /* $goodsname =request('goodsname');
            //查找goods表
            $goods = Goods::where('id', '>', 0)
                ->where(function ($query) use ($goodsname) {
                    if ($goodsname != null) {
                        $query->where('goods_name', 'like', "%{$goodsname}%")->get();
                    }
                })
                ->paginate(12);
            //dd($goods);
            return view('Home.lists', compact('goods', 'goodsname', 'drpListPrice', 'drpListCZX', 'drpListCZ', 'drplistzct'));
        }*/
}
