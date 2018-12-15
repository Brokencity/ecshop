<?php

namespace App\Http\Controllers\Admin;

use App\category;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AddController extends Controller
{
    //添加分类
    public function add_cate(){
        $category = category::orderBy('path')->get();
        //dd($category);
            if(request()->isMethod('get')){

                //$category = count()


                //写入缓存
                request()->flash();
                //接受提交上来的id
                //$res = request()->all();
                //dd($res);
            }

            return view('Admin.category.Add_cate', compact('category'));
        }
}
