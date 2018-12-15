<?php

namespace App\Http\Controllers\Admin;

use App\MemberLevel;
use Illuminate\Http\Request;
use App\Member;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberListController extends Controller
{
    //管理员列表
    public function memberList()
    {
        /*dd(request()->all());die;*/
        //接收传来的数据
        $keyword = trim(request('keyword'));  //关键字
        $levelWord = trim(request('levelWord'));  //等级查询
        $active = request('active');          //状态值
        $start_time = request('start_time');  //开始日期
        $end_time = request('end_time');      //结束日期
        /*dd($levelWord);*/
        /*dd($start_time,$end_time);die;*/
        request()->flash();   //数据写入闪存
        $member =Member::where(function($query) use($keyword){   //通过关键字查询
            if(!$keyword == null)
            {
                $query->where('username','like',"%$keyword%");
            }
        })

            ->where(function ($query) use($levelWord){   //通过会员等级查询
                if(!$levelWord==null){
                    $query->where('level_id','like',"%$levelWord%");
                }
            })
            ->where(function($query) use($active)                   //通过状态查询
            {
                if($active > 0)
                {
                    $query->where('active',$active);
                }
            })
            ->where(function($query) use($start_time,$end_time)
            {   //查询日期的三种状态
                if($start_time && !$end_time)     //开始日期存在而结束日期不存在
                {
                    $query->where('add_time','>=',strtotime($start_time));
                }
                else if(!$start_time && $end_time)  //开始日期不存在而结束日期存在
                {
                    $query->where('add_time','<=',strtotime($end_time));
                }
                else if($start_time && $end_time)   //开始日期和结束日期都存在
                {
                    $query->whereBetween('add_time',[strtotime($start_time),strtotime($end_time)]);
                }
            })
            ->orderBy('add_time')
            ->paginate(5);
        //dd($member);

        //dd($res);
        //插入会员等级字段member_level
       foreach($member as $k=>$v)
        {
            $res=MemberLevel::join('member','member_level.id','=','member.level_id')
                ->select('level_name')
                ->where('member_level.id',$v->level_id)
                ->first();
            $levelName = $res['level_name'];
            $member[$k]->member_level = $levelName;
        }
        //dd($member);
        return view('admin.member.memberList',compact('member','keyword','levelWord','active','start_time','end_time'));
    }
    //删除会员
    public function delMember($id){
        /*dd($id); die;*/
        //通过主键值删除
        $arr=Member::destroy($id);
        if($arr>0){
            //删除成功
            return response()->json(['status'=>'ok','msg'=>'会员删除成功']);
        }else{
            //删除失败
            return response()->json(['status'=>'error','msg'=>'会员删除失败']);
        }
    }
    //会员状态 激活or冻结
    public function active($id,$active){
        $actives=$active==1?'激活':'冻结';
        /*dd($actives);die;*/
        $res=Member::where('id',$id)->update(['active'=>$active]);
        if($res){
            return response()->json(['status'=>'ok','msg'=>'会员'.$actives.'成功', 'active'=>$active,'id'=>$id]);
        }else{
            return response()->json(['status'=>'error','msg'=>'会员'.$actives.'失败']);
        }
    }
    //删除选中数据
    public function allDelete(){
        //根据主键删除所选数据
        $arr=Member::destroy(request('chk'));
        if($arr>0) {
                return response()->json(['status' => 'ok', 'msg' => '删除选中记录成功']);
        }else{
                return response()->json(['status' => 'error', 'msg' => '删除选中记录失败']);
        }
    }

}
