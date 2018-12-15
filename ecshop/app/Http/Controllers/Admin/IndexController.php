<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    //后台页面
    public function index(){
        return view('admin.index.index');
    }
    //顶部页面
    public function top(){
        return view('admin.index.top');
    }
    //左边页面
    public function left(){
        return view('admin.index.left');
    }
    //主页面
    public function main(){
        $id = Session::get('aid');
        $admin = Admin::where('id',$id)->first();
        //dd($admin);
        $arr = $_SERVER;
        //dd($arr);
        return view('admin.index.main',compact('admin','arr'));
    }
    //后台页面
    public function footer(){
        return view('admin.index.footer');
    }
}
