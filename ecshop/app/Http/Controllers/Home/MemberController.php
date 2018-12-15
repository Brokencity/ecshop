<?php

namespace App\Http\Controllers\Home;

use App\Address;
use App\category;
use App\Member;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManagerStatic;

class MemberController extends Controller
{
    //个人中心首页
    public function member_index()
    {
        if(session('mid')){
            $id=session('mid');
           $result= Member::where('id',$id)->first();
           $cate = category::where('pid',0)->get();
            return view('Home.member.member_index',compact('result','cate'));
        }
        return view('Home.member.member_index');
    }
    //会员收货地址管理
    public function member_addr()
    {
        $mid = session('mid');
        if(request()->isMethod('post'))
        {
            $truename = trim(request('truename'));
            $truemobile = trim(request('truemobile'));
            $addres = request('province').request('city').request('district').request('street');
            $active = request('active');

            $res = Address::insertGetId(['mid'=>$mid,'truename'=>$truename,'truemobile'=>$truemobile,'address'=>$addres]);
            if($res)
            {
                if(!$active==null){
                    if(Address::where('id',$res)->where('mid',$mid)->update(['active'=>1])){
                        if(Address::where('mid',$mid)->where('id','!=',$res)->update(['active'=>2])) {
                            return back()->with(['status'=>'ok','msg'=>'收货地址添加成功']);
                        }
                    }else{
                        return back()->with(['status'=>'error','msg'=>'收货地址添加失败']);
                    }
                }else{
                    return back()->with(['status'=>'ok','msg'=>'收货地址添加成功']);
                }
            }
            else
            {
                return back()->with(['status'=>'error','msg'=>'收货地址添加失败']);
            }
        }
        else
        {
            $address = Address::where('mid',$mid)->get();
            return view('Home.member.member_addr',compact('address'));
        }

    }
    //会员收货地址修改
    public function member_editAdd($id=0)
    {
        //dd($id);
        if(request()->isMethod('post'))
        {
            $truename = trim(request('truename'));
            $truemobile = trim(request('truemobile'));
            $addres = request('province').request('city').request('district').request('street');
            $res = Address::where('id',$id)->update(['truename'=>$truename,'truemobile'=>$truemobile,'address'=>$addres]);

            if($res)
            {
                //dd($res);
                return back()->with(['status'=>'ok','msg'=>'收货地址修改成功']);
            }
            else
            {
                return back()->with(['status'=>'error','msg'=>'收货地址修改失败']);
            }
        }
        else
        {
            $addr = Address::where('id',$id)->first();

            return view('Home.member.member_editaddr',compact('addr'));
        }

    }
    //会员设置常用地址
    public function member_address($id=0){

            $mid=session('mid');

                if(Address::where('id',$id)->where('mid',$mid)->update(['active'=>1])){
                    if(Address::where('mid',$mid)->where('id','!=',$id)->update(['active'=>2])){
                        return response()->json(['status'=>'ok','msg'=>'设置成功']);
                    }else{
                        return response()->json(['status'=>'error','msg'=>'设置失败']);
                    }
                }else{
                    return response()->json(['status'=>'error','msg'=>'请勿重复设置']);
                }

    }
    //会员ID删除收货地址
    public function delete_address($id=0){

        if(Address::destroy($id)){
            return response()->json(['status'=>'ok','msg'=>'删除成功']);
        }else{
            return response()->json(['status'=>'error','msg'=>'删除失败']);
        }
    }
    //会员头像上传
    public function member_avatar()

    {
        $id=session('mid');

        if(request()->isMethod('post')){
            if(request()->hasFile('image')){
                if($avatar=$this->upload(request()->file('image'))){
                    $data['avatar_dir']=$avatar['img_dir'];
                    $data['avatar']=$avatar['img_name'];
                    if(Member::where('id',$id)->update($data)){
                        return response()->json(['status'=>'ok','msg'=>'头像上传成功']);
                    }else{
                        return response()->json(['status'=>'error','msg'=>'头像上传失败']);
                    }
                }else{
                    return response()->json(['status'=>'error','msg'=>'头像上传失败']);
                }

            }else{
                return response()->json(['status'=>'error','msg'=>'请选择头像']);
            }

        }else{
            $avatar=Member::select('avatar','avatar_dir')->where('id',$id)->first();
            return view('Home.member.member_avatar',compact('avatar'));
        }

    }
    //图片上传
    public function upload($pic)
    {
        //获取扩展名
        $ext = $pic->getClientOriginalExtension();
        //目标文件名称
        $name = date('YmdHis') . '-' . uniqid() . '.' . $ext;
        //保存的目录
        $dir = config('filesystems.MemberAvatar');
        //保存
        if ($pic->move($dir, $name)){
            //缩放
            $img = ImageManagerStatic::make($dir . $name)->resize(90,90)->save($dir . '90_' . $name);

            //销毁
            $img->destroy();
            //返回
            return ['img_dir' => $dir, 'img_name' => $name];
        } else {
            return false;
        }
    }
    //会员收藏
    public function member_collect()
    {
        return view('Home.member.member_collect');
    }
    //会员资料修改
    public function member_info()
    {
        if(request()->isMethod('post'))
        {
            $id = session('mid');
            //闪存数据
            request()->flash();
            //表单验证
            $this->validate(request(),[
                'email'=>'bail|required|unique:member|regex:/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
                'district'=>'bail|required|different:市、县级市、县',
                'street'=>'bail|required',
                'sex'=>'bail|required',
                'mobile'=>['bail','unique:member','required','regex:/^[0-9]/','max:11','min:11']
                ],[
                    'email.required'=>'邮箱不能为空',
                    'email.unique'=>'邮箱已被占用',
                    'email.regex'=>'邮箱格式不正确',
                    'district.required'=>'请选择地址',
                    'district.different'=>'请选择地址',
                    'street.required'=>'详细地址不能为空',
                    'sex.required'=>'请选择性别',
                    'mobile.required'=>'手机号不能为空',
                    'mobile.regex'=>'手机号只能输入数字',
                    'mobile.max'=>'手机号最多输入11位',
                    'mobile.min'=>'手机号最少输入11位',
                    'mobile.unique'=>'手机号已被使用'
            ]);
            //添加会员资料逻辑
            $data['email']=trim(request('email'));
            $data['site']=trim(request('province')).trim(request('city')).trim(request('district')).trim(request('street'));
            $data['mobile']=trim(request('mobile'));
            $data['sex']=trim(request('sex'));
            if(Member::where('id',$id)->update($data)){
                return back()->with(['status'=>'ok','msg'=>'资料自改成功']);
            }else{
                return back()->with(['status'=>'error','msg'=>'资料修改失败']);
            }
        }
        else
        {

            return view('Home.member.member_info');
        }

    }
    //会员订单
    public function member_order()
    {
        return view('Home.member.member_order');
    }
    //会员订单详情
    public function member_order_detail($id)
    {
        //dd($id);
        return view('Home.member.member_order_detail');
    }
    //会员修改密码
    public function member_pwd()
    {
        $id = session('mid');
        $oldPassword = md5(trim(request('oldPassword')));

        if(request()->isMethod('post'))
        {
           request()->flash();
           $this->validate(request(),
               [
                   'oldPassword' => 'bail|required|',
                   'password' => 'bail|required|min:6|max:15',
                   'repwd' => 'bail|required|same:password',

               ],
               [
                   'oldPassword.required' => '原密码不能为空',
                   'password.required' => '新密码不能为空',
                   'password.min' => '密码长度不能少于6位',
                   'password.max' => '密码长度最长不能超过15位',
                   'repwd.required' => '重复密码不能为空',
                   'repwd.same' => '重复密码输入不正确'
               ]);
           $oldpwd = Member::select('password')->where('id',$id)->first();
           if($oldpwd['password'] == $oldPassword)
           {
                $res = Member::where('id',$id)->update(['password'=>md5(trim(request('password')))]);
                if($res)
                {
                    return back()->with(['status'=>'ok','msg'=>'会员密码修改成功']);
                }
                else
                {
                    return back()->with(['status'=>'error','msg'=>'会员密码修改失败']);
                }
           }
           else
           {
               return back()->with(['status'=>'error','msg'=>'原密码输入不正确']);
           }
        }
        else
        {
            return view('Home.member.member_pwd');
        }

    }
}
