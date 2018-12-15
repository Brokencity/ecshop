<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    //管理员列表
    public function adminList()
    {
        /*dd(request()->all());die;*/
        //接收传来的数据
        $keyword = trim(request('keyword'));  //关键字
        $active = request('active');          //状态值
        $start_time = request('start_time');  //开始日期
        $end_time = request('end_time');      //结束日期
        //dd($start_time,$end_time);
        request()->flash();   //数据写入闪存
        $admin = Admin::where(function($query) use($keyword){   //通过关键字查询
            if(!$keyword == null)
            {
                $query->where('username','like',"%$keyword%");
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
        //dd($admin);
        return view('admin.admin.adminList',compact('admin','keyword','active','start_time','end_time'));
    }

    //添加管理员
    public function addAdmin()
    {   //接收数据
        if(request()->isMethod('post'))
        {   //将数据写入闪存
            request()->flash();
            //laravel下的表单验证
            $this->validate(request(),
                ['username'=>'bail|required|unique:admin|max:12',  //bail 第一个验证没通过后面不再验证
                 'password'=>'bail|required|min:6|max:16',
                 'repwd'=>'bail|required|same:password'
                ],
                [
                 'username.required'=>'管理员名称不能为空',
                 'username.unique'=>'管理员名称已存在',
                 'username.max'=>'管理员名称最长不能超过12个字符',
                 'password.required'=>'密码不能为空',
                 'password.min'=>'密码长度不能少于6个字符',
                 'password.max'=>'密布长度不能超过16个字符',
                 'repwd.required'=>'重复密码不能为空',
                 'repwd.same'=>'重复密码输入不正确',
                ]);
            //将数据播入到数据表
            $data =['username'=>trim(request('username')),'password'=>md5(trim(request('password'))),'add_time'=>time()];
            $res = Admin::insert($data);
            if($res)
            {
                return back()->with(['status'=>'ok','msg'=>'成功添加管理员','url'=>route('bg/admin/adminList')]);
            }
            else
            {
                return back()->with(['status'=>'error','msg'=>'添加管理员失败']);
            }
        }else
        {
            //显示添加管理员页面
            return view('admin.admin.addAdmin');
        }
    }
    //更改管理员密码
    public function changePwd($id)
    {

        if(request()->isMethod('post'))
        {   //将数据写入闪存
            request()->flash();
            //laravel表单验证
            $this->validate(request(),
            [
             'password'=>'bail|required|min:6|max:16',
             'repwd'=>'bail|required|same:password'
            ],
            [
             'password.required'=>'密码不能为空',
             'password.min'=>'密码长度不能少于6个字符',
             'password.max'=>'密码长度不能大于16个字符',
             'repwd.required'=>'重复密码不能为空',
             'repwd.same'=>'重复密码输入错误'
            ]);
            $res = Admin::where('id',$id)->update(['password'=>md5(trim(request('password')))]);
            if($res)
            {
                return back()->with(['status'=>'ok','msg'=>'密码更改成功']);
            }
            else
            {
                return back()->with(['status'=>'error','msg'=>'密码更改失败']);
            }
        }
        else
        {
            $admin = Admin::select('id','username')->where('id',$id)->first();
            //dd($admin);
            return view('admin.admin.changePwd',compact('admin'));
        }

    }
    //删除管理员
    public function delete($id)
    {
        //dd($id);
        $res = Admin::destroy($id);
        if($res>0)
        {
            return response()->json(['status'=>'ok','msg'=>'管理员删除成功']);
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'管理员删除失败']);
        }
    }
    //更改管理员状态
    public function active($id,$active)
    {
        $acti = $active==1?'激活':'禁用';
        $res = Admin::where('id',$id)->update(['active'=>$active]);
        if($res)
        {
            return response()->json(['status'=>'ok','msg'=>'管理员'.$acti.'成功','active'=>$active,'id'=>$id]);
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'管理员'.$acti.'失败']);
        }
    }
    //删除多条数据
    public function multiDelete()
    {
        $res = Admin::destroy(request('chk'));
        if($res)
        {
            return response()->json(['status'=>'ok','msg'=>'删除选中记录成功']);
        }
        else
        {
            return response()->json(['status'=>'error','msg'=>'删除选中记录失败']);
        }
    }
    //编辑管理员名字
    public function editAdminName()
    {
        $id = request('id');
        $username = trim(request('username'));
        if(request()->ajax())
        {
            $this->validate(request(),
            [
                'username' => 'bail|required|unique:admin|max:12'
            ],
            [
                'username.required'=>'管理员名称不能为空',
                'username.unique'=>'管理员名称已存在',
                'username.max'=>'管理名称长度不能超过12个字符'
            ]);
            $res = Admin::where('id',$id)->update(['username'=>$username]);
            if($res)
            {
                return response()->json(['status'=>'ok','msg'=>'管理员名称更改成功']);
            }
            else
            {
                return response()->json(['status'=>'error','msg'=>'管理员名称更改失败']);
            }
        }
    }

}
