<?php

namespace App\Http\Controllers\Admin;

use App\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{   //后台订单管理
    //订单列表
    public function orderList()
    {   //订单查询
        //接收数据
        $orderSyn = trim(request('orderSyn'));        //订单号
        $status = request('status');                //订单状态
        $start_time = request('start_time');        //开始时间
        $end_time = request('end_time');            //结束时间
        request()->flash();//数据写入闪存
        $ress = Order::where('status',1)->get();
        //dd($ress);
        foreach($ress as $k=>$v)
        {
            $time1 = time();
            $time2 = strtotime("+24 hours",$v->add_time);
            //dd($time2);
            if($time1 > $time2)
            {
                //dd($time1);
                Order::where('id',$v->id)->update(['status'=>6]);
            }
        }
        $orders = Order::where(function($query) use($orderSyn)
        {
                  if($orderSyn != null)
                  {
                      $query->where('order_syn','like',"%$orderSyn%");
                  }
        })
        ->where(function($query) use($status)
        {
                  if($status >0)
                  {
                      $query->where('status',$status);
                  }
        })
        ->where(function($query) use($start_time,$end_time)
        {
                  if($start_time && !$end_time)
                  {
                      $query->where('add_time','>=',strtotime($start_time));
                  }
                  if($end_time && !$start_time)
                  {
                      $query->where('add_time','<=',strtotime($end_time));
                  }
                  if($start_time && $end_time)
                  {
                      $query->whereBetween('add_time',[strtotime($start_time),strtotime($end_time)]);
                  }
        })
        ->orderBy('add_time','desc')
        ->paginate(10);
        //dd($orders);

        $active = ['未支付','待发货','待收货','完成交易','用户取消订单','支付时间过期取消'];
        return view('admin.order.orderList',compact('orders','orderSyn','memberName','status','start_time','end_time','active'));
    }
    //
}
