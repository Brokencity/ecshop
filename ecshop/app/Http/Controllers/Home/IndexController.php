<?php

namespace App\Http\Controllers\Home;

use App\category;
use App\Goods;
use App\GoodsImage;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    //前台首页
    public function index()
    {
        $goods = Goods::orderBy('saled_num','desc')->take(4)->get();
        //dd($goods);
        return view('home.index',compact('goods'));
    }
    //用户退出
    public function logout()
    {
        session()->flush();
        return response()->json(['status'=>'ok','msg'=>'成功退出','url'=>route('/')]);
    }
    //品牌文化
    public function brand()
    {
        $cate = category::where('pid',0)->get();
        //dd($cate);
        return view('Home.brand',compact('cate'));
    }
    //店铺
    public function store(){
        return view('Home.pinpai.store');
    }
    //知名明星
    public function star(){
        return view('Home.pinpai.star');
    }
    //品牌理念
    public function idea()
    {
        return view('Home.pinpai.idea');
    }
    //真爱验证
    public function love()
    {
        return view('Home.pinpai.love');
    }
    //相守一生
    public function verify()
    {
        return view('Home.pinpai.verify');
    }
    //真爱协议
    public function deal()
    {
        return view('Home.pinpai.deal');
    }
    //工匠雕刻
    public function engrave()
    {
        return view('Home.pinpai.engrave');
    }
    //商品详情页
    public function detail($id)
    {
        //dd(session());
        $goods = Goods::where('id',$id)->first();
        $images = GoodsImage::where('gid',$id)->get();
        //dd($images);
        return view('Home.detail',compact('goods','images'));
    }
    //帮助页面
    public function help()
    {
        $cate=category::where('pid',0)->get();
        //dd($cate);
        return view('Home.help.help',compact('cate'));
    }
    //常见问题
    public function question()
    {
        return view('Home.help.question');
    }
    //最新活动
    public function active()
    {
        return view('Home.help.active');
    }
    //会员密码找回
    public function forget()
    {
        return view('Home.forget');
    }
}
