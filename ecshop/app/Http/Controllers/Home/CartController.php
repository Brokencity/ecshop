<?php

namespace App\Http\Controllers\Home;

use App\Address;
use App\Cart;
use App\Goods;
use App\Order;
use App\OrderGoods;
use App\Protocol;
use function foo\func;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    //购物车页面
    public function cart()
    {
        $mid = session('mid');
        $goods = Goods::from('goods as g')
            ->join('cart as c','g.id','=','c.gid')
            ->select('g.*','c.*')
            ->where('c.mid',$mid)
            ->get();
        //dd($goods);
        //
        Protocol::where('mid',$mid)->delete();
        Cart::where('mid',$mid)->where('active',2)->update(['active'=>1]);
        return view('Home.cart.cart',compact('goods'));
    }
    public function layout()
    {
        $mid = session('mid');
        $num = Cart::where('mid',$mid)->select('buy_num')->sum('buy_num');
        return view('Home.public.layout',compact('num'));
    }
    //添加购物车页面
    public function toCart($gid)
    {
        $buynum = request('buynum');
        if(session('mid') != null)
        {
           $mid = session('mid');
           $res = Cart::whereRaw('mid = ? and gid = ?',[$mid,$gid])->first();
           if($res)
           {
                $res = Cart::where('mid',$mid)->update(['buy_num'=>$buynum,'add_time'=>time()]);
                if($res)
                {
                    return response()->json(['status'=>'ok','msg'=>'商品成功加入购物车']);
                }
                else
                {
                    return response()->json(['status'=>'error','msg'=>'商品加入购物车失败']);
                }
           }
           else
           {
                $res = Cart::insert(['mid'=>$mid,'gid'=>$gid,'buy_num'=>$buynum,'add_time'=>time()]);
                if($res)
                {
                    return response()->json(['status'=>'ok','msg'=>'商品成功加入购物车']);
                }
                else
                {
                    return response()->json(['status'=>'error','msg'=>'商品加入购物车失败']);
                }
           }
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'请先登录']);
        }

    }
    //商品展示页面
    public function lists()
    {
        //接收数据
        $goodsname = trim(request('goodsname',''));     //参数二：默认值
        $drpListPrice = trim(request('drpListPrice',-1));
        $drplistzct = trim(request('drplistzct',-1));
        $drpListCZ = trim(request('drpListCZ',-1));
        $drpListCZX = trim(request('drpListCZX',-1));
        request()->flash();
//        dd(request()->all());
        //dd($goodsname);
        //按销量查找
        if($drpListCZX == -1){
            $goods = Goods::where('id','>',0)->paginate(12);
            return view('Home.lists', compact('goods', 'goodsname','drpListPrice','drpListCZX','drpListCZ','drplistzct'));
        }else{
            $drpListCZX = $drpListCZX == 1?'saled_num':'price';
            //dd($drpListCZX);
        //搜索
        $goods = Goods::where('id', '>', 0)
            ->where(function ($query) use ($goodsname) {
                if ($goodsname != null) {
                    $query->where('goods_name', 'like', "%{$goodsname}%")->get();
                }
            })
            //按照价格区间
            ->where(function($query) use($drpListPrice){
                if ($drpListPrice == -1) {
                    $query->where('price', '>', 0)->get();
                    //查找10000一下
                } elseif ($drpListPrice == 1) {
                    $query->where('price', '<', 10000)->get();
                    //查找20000一下
                } elseif ($drpListPrice == 2) {
                    $query->whereBetween('price', [10000, 20000])->get();
                } elseif ($drpListPrice == 3) {
                    $query->whereBetween('price', [20000, 30000])->get();
                } else {
                    $query->where('price', '>', 30000)->get();
                }
            })
            //查找全部 drplistzct 钻石重量
            ->where(function ($query) use ($drplistzct) {
                if ($drplistzct == -1) {
                    $query->where('diamond_all', '>', 0)->get();
                    //查找10克拉一下
                } elseif ($drplistzct == 1) {
                    $query->where('diamond_all', '<', 10)->get();
                    //查找10-30克拉
                } elseif ($drplistzct == 2) {
                    $query->whereBetween('diamond_all', [10, 30])->get();
                    //查找30-50克拉
                } elseif ($drplistzct == 3) {
                    $query->whereBetween('diamond_all', [30, 50])->get();
                    //查找50分---1克拉之间
                } elseif ($drplistzct == 4) {
                    $query->whereBetween('diamond_all', [50, 100])->get();
                    //查找一克拉以上的
                } else {
                    $query->where('diamond_all', '>', 100)->get();
                }
            })
            //查找材质属性
            ->where(function ($quest) use ($drpListCZ) {
                if ($drpListCZ == -1) {
                    $quest->where('goods_texture', '>', 0)->get();
                } elseif ($drpListCZ == 1) {
                    $quest->where('goods_texture', '=', '铂金')->get();
                } else {
                    $quest->where('goods_texture', '=', '18K白金')->get();
                }
            })
            //查找全部 drplistzct 钻石重量
            ->where(function ($query) use ($drplistzct) {
                if ($drplistzct == -1) {
                    $query->where('diamond_all', '>', 0)->get();
                    //查找10克拉一下
                } elseif ($drplistzct == 1) {
                    $query->where('diamond_all', '<', 10)->get();
                    //查找10-30克拉
                } elseif ($drplistzct == 2) {
                    $query->whereBetween('diamond_all', [10, 30])->get();
                    //查找30-50克拉
                } elseif ($drplistzct == 3) {
                    $query->whereBetween('diamond_all', [30, 50])->get();
                    //查找50分---1克拉之间
                } elseif ($drplistzct == 4) {
                    $query->whereBetween('diamond_all', [50, 100])->get();
                    //查找一克拉以上的
                } else {
                    $query->where('diamond_all', '>', 100)->get();
                }
            })
            //按排序查找
            ->where(function ($quest) use ($drpListCZX) {
                if ($drpListCZX == 1) {
                    $quest->where('goods_texture', '=', '铂金');
                } else {
                    $quest->where('goods_texture', '=', '18K白金');
                }
            })
            ->orderBy($drpListCZX,'desc')
            ->paginate(12);
        return view('Home.lists', compact('goods', 'goodsname', 'drpListPrice', 'drpListCZX', 'drpListCZ', 'drplistzct'));
        }
    }
    //购物车订单详情页
    public function cartOrder()
    {
        $mid = session('mid');
        if(request()->isMethod('post'))
        {
            $addressId = request('addressID');
            $mid = session('mid');
            $orderPrice = request('orderPrice');
            $orderNote = request('orderNote') != null ? request('orderNote') : '';
            $addTime = time();
            $order_syn = date('YmdHis') . substr(str_shuffle(mt_rand(1, 10000)), 0, 14);

            $oid = Order::insertGetId(['mid' => $mid, 'order_syn' => $order_syn, 'order_price' => $orderPrice, 'add_time' => $addTime, 'address_id' => $addressId, 'ordernote' => $orderNote]);
            if ($oid)
            {
                Session::put('order_syn',$order_syn);
                Session::put('orderPrice',$orderPrice);
                $arr = Cart::where('mid',$mid)->where('active',2)->get();
                //dd($arr);
                foreach($arr as $k=>$v)
                {
                    OrderGoods::insert(['oid'=>$oid,'gid'=>$v->gid,'num'=>$v->buy_num]);
                    Cart::where('cart_id',$v->cart_id)->delete();
                }
                return response()->json(['status'=>'ok','msg'=>'订单生成成功，去选择支付方式','url'=>route('cart/cartOrderSuccess')]);
            }
            else
            {
                return response()->json(['status'=>'error','msg'=>'订单生成失败']);
            }
        }
        else
        {
            $adres = Address::whereRaw('mid = ? and active = ?',[$mid,1])->first();
            $goods = Goods::from('goods as g')
                ->join('cart as c','g.id','=','c.gid')
                ->select('g.*','c.*')
                ->where('c.mid',$mid)
                ->where('c.active',2)
                ->get();

            foreach($goods as $k=>$v)
            {
                $money =  $v->price * $v->buy_num;
                $goods[$k]->prices= $money;
            }
            //dd($goods);

            return view('Home.cart.cart_order',compact('adres','goods'));
        }

        }


    //购物车真爱协议页
    public function cartAgreement()
    {   //dd(request('chk'));
        if(request()->isMethod('post'))
        {
            //dd(request('sirName'));
            $sirName = request('sirName');
            $ladyName = request('ladyName');
            $sirCode = request('sirCode');
            $shebirthday= request('shebirthday') !=null?request('shebirthday'):'';
            $shedate1= request('shedate1') !=null?request('shedate1'):'';
            $shedate2= request('shedate2') !=null?request('shedate2'):'';
            $res = Protocol::where('sirCode',$sirCode)->where('mid',session('mid'))->first();
            if($res)
            {
                return response()->json(['status'=>'error','msg'=>'对不起，您已经签订过爱情协议！']);
            }
            else
            {
                $res = Protocol::insertGetId(['sirName'=>$sirName,'ladyName'=>$ladyName,'sirCode'=>$sirCode,'shebirthday'=>$shebirthday,'shedate1'=>$shedate1,'shedate2'=>$shedate2,'mid'=>session('mid')]);
                if($res)
                {
                    return response()->json(['status'=>'ok','msg'=>'先生已成功签订爱情协议']);
                }
                else
                {
                    return response()->json(['status'=>'error','msg'=>'爱情协议签订出错，请确认信息']);
                }
            }

        }
        else
        {
            $result = Protocol::where('mid',session('mid'))->first();
            return view('Home.cart.cart_agreement',compact('result'));
        }
    }
    //购物车订单完成页
    public function CartOrderSuccess()
    {

        $orders = Order::where('order_syn',session('order_syn'))->first();
        //dd($orders);
        return view('Home.cart.cart_order_success',compact('orders'));

    }
    //购物车商品单件删除
    public function cartDelete($id)
    {   //dd($id);
        $res = Cart::where('cart_id',$id)->delete();
        if($res)
        {
            return response()->json(['status'=>'ok','msg'=>'购物车商品删除成功']);
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'购物车商品删除失败']);
        }
    }
    //购物车商品多件删除
    public function cartDeletes()
    {
        $arr = request('chk');
        $res = Cart::whereIn('cart_id',$arr)->delete();
        if($res)
        {
            return response()->json(['status'=>'ok','msg'=>'购物车选中商品删除成功']);
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'购物车选中商品删除失败']);
        }
    }
    //提交真爱协议页时选中商品状态更改
    public function cartActive()
    {
        $arr =request('chk');
        $res = Cart::whereIn('cart_id',$arr)->where('active','1')->update(['active'=>2]);

        if($res)
        {
            return response()->json(['url'=>route('cart/cartAgreement')]);
        }

    }
    //立即购买
    public function buyNow($gid)
    {
        $buyNum = request('buynum');
        if(session('mid') != null)
        {
            $mid = session('mid');
            $res = Cart::whereRaw('mid = ? and gid = ?',[$mid,$gid])->first();
            if($res)
            {
                $res = Cart::where('mid',$mid)->update(['buy_num'=>$buyNum,'add_time'=>time(),'active'=>2]);
                if($res)
                {
                    return response()->json(['status'=>'ok','msg'=>'立即购买成功，跳转到真爱协调页','url'=>route('cart/cartAgreement')]);
                }
                else
                {
                    return response()->json(['status'=>'error','msg'=>'立即购买失败，请确认商品无问题']);
                }

            }
            else
            {
                $res = Cart::insert(['mid'=>$mid,'gid'=>$gid,'buy_num'=>$buyNum,'add_time'=>time(),'active'=>2]);
                if($res)
                {
                    return response()->json(['status'=>'ok','msg'=>'立即购买成功，跳转到真爱协调页','url'=>route('cart/cartAgreement')]);
                }
                else
                {
                    return response()->json(['status'=>'error','msg'=>'立即购买失败，请确认商品无问题']);
                }

            }
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'请先登录']);
        }
    }
}
