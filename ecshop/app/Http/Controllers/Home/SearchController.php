<?php

namespace App\Http\Controllers\Home;

use App\Category;
use App\Goods;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class SearchController extends HomeBaseController
{
    //搜索页
    public function index(){
        //闪存请求数据，进行数据保持
        request()->flash();
        //商品名称
        $goodsname=trim(request('goodsname'));
        dd($goodsname);
        //价格
        $price=request('price');
        //商品分类
        $cid=request('cid');
        $pid=request('pid');
        if($pid){
            $cate=Category::find($pid);
            $path=$cate->path;
            $catename=$cate->catename;
        }else{
            if($cid){
                $cate=Category::find($cid);
                $path=$cate->path;
                $catename=$cate->catename;
                $pid=$cate->pid;
            }else{
                $pid=0;
                $path=null;
                $catename=null;
            }
        }

        //查询分类
        $sub_cates=Category::where('pid',$pid)->get();

        //根据搜索条件查询数据
        $data=Goods::from('goods as g')
            ->leftjoin('category as c','g.cid','=','c.id')
            ->select('g.*','c.catename')
            ->where(function($query) use($goodsname){
                if($goodsname){
                    $query->where('goodsname','like',"%{$goodsname}%")->orWhere('keywords','like',"%{$goodsname}%");
                }
            })
            ->where(function($query) use($path){
                if($path){
                    //查询所有子分类
                    $cids=Category::where('path','like',"{$path},%")->orWhere('path',$path)->pluck('id')->toArray();
                    $query->whereIn('g.cid',$cids);
                }
            })
            ->where(function($query) use($price){
                if($price){
                    $arr=explode('-',$price);
                    if(count($arr)>1){
                        $query->whereBetween('price',[$arr[0],$arr[1]]);
                    }else{
                        $query->where('price','>=',10000);
                    }
                }
            })
            ->paginate(5);

        return view('home.search.index',compact('data','goodsname','pid','cid','price','sub_cates'));
    }
}