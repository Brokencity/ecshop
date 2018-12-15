<?php

namespace App\Http\Controllers\Home;

use App\Goods;
use App\GoodsCollect;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{   //商品加入收藏
    public function goods_collect($id)
    {
        //dd(session('mid'));
        if(session('mid') != null)
        {
            $mid = session('mid');
            $gid = $id;
            //dd($gid);
            $res = GoodsCollect::insertGetId(['mid'=>$mid,'gid'=>$gid]);
            if($res)
            {
                return response()->json(['status'=>'ok','msg'=>'商品成功加入收藏']);
            }
            else
            {
                return response()->json(['status'=>'error','msg'=>'商品未能成功加入收藏']);
            }
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'请先登录']);
        }
    }

}
