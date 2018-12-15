<?php

namespace App\Http\Controllers\Admin;

use App\category;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
//通过pid异步请求子分类(分类三级联动中使用)
    public function getCateByPid(){
        $cate=category::where('pid',request('pid'))->get();
        return response()->json($cate);
    }
}
