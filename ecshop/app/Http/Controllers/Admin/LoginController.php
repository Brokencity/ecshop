<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //登录
    public function login(){
        if(request()->ajax()){
            //登录业务逻辑处理
            //dd(request()->all());
            //判断验证码是否正确
            if(session()->get('phrase')==trim(request('captcha'))){
                //判断用户名密码是否正确
                $res=Admin::where('username',trim(request('username')))->where('password',md5(trim(request('password'))))->first();
                /*dd($res);die;*/
                if($res){
                    //判断管理员是否被禁用
                    if($res['active']==1){
                        //管理员id
                        $id=$res->id;
                        //将登录信息写session
                        session()->put(['aid'=>$id,'aname'=>$res->username]);
                        //删除登录验证码session
                        session()->forget('phrase');
                        //更新登录时间,登录ip
                        Admin::where('id',$id)->update(['login_time'=>time(),'login_ip'=>request()->getClientIp()]);
                        //更新登录次数
                        Admin::where('id',$id)->increment('login_num');

                        return response()->json(['status'=>'ok','msg'=>'登录成功','url'=>route('bg/index')]);
                    }else{
                        return response()->json(['status'=>'error','msg'=>'此账号已被禁用，请联系超级管理员']);
                    }

                }else{
                    return response()->json(['status'=>'error','msg'=>'用户名或密码不正确']);
                }

            }else{
                return response()->json(['status'=>'error','msg'=>'验证码错误']);
            }
        }else{
            //显示登录页面
            return view('admin.login.login');
        }
    }


    //退出
    public function logout(){
        //清除session中的登录信息
        session()->forget('aid');
        session()->forget('aname');
        //退出成功，跳转后台的登录页面
        return response()->json(['status'=>'ok','msg'=>'成功退出','url'=>route('bg')]);
    }

    //后台验证码
    public function captcha(){
        //生成一个自定义的字符串对象
        $phraseBuilder=new PhraseBuilder(3,'0123456789');
        //通过生成的字符串对象来实例化验证码对象
        $builder=new CaptchaBuilder(null,$phraseBuilder);
        //产生验证码
        $builder->build('114','46'); //指定验证码的宽高
        //将产生的验证码随机字符串写入Session
        session()->put('phrase',$builder->getPhrase());
        //输出验证码图片
        header("Content-Type:image/png");
        $builder->output();
    }
}
