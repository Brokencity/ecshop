<?php

namespace App\Http\Controllers\Home;

use App\Cart;
use App\Member;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    //前端登陆
    public function login(){
        /*dd(request()->all());die;*/
        //dd(request()->method());
        //dd(request()->isMethod('POST'));
        //判断提交方式是否为post
        if(request()->isMethod('POST')){
            //判断验证码是否正确
            if(session()->get('phrase')==trim(request('captcha'))){
                //用户信息存入闪存
                request()->flash();
                /*dd(request()->all());die;*/
                //用户登录业务逻辑
                $username=trim(request('username'));
                $password=trim(md5(request('password')));

                //清除session中存入的验证码
                session()->forget('phrase');

                /*dd($data);*/
                //判断用户名密码是否正确
                $str=Member::where('username', $username)->where('password',$password)->first();
                session(["username"=>$username]);
                session(['mid'=>$str['id']]);
                $cartNum = Cart::where('mid',$str['id'])->sum('buy_num');
                //dd($cartNum);
                session(['cartNum'=>$cartNum]);
                //dd(session());
                /*dd($str['active']);die;*/
                if($str){
                    //判断用户是否被冻结
                    /*dd($str['active']);die;*/
                    if($str['active']==1){
                        return back()->with(['status'=>'ok','msg'=>'登录成功','url'=>route('/')]);
                    }else{
                        return back()->with(['status'=>'error','msg'=>'您的账号已被冻结，请联系客服！']);
                    }
                }else {
                    return back()->with(['status'=>'error','msg'=>'用户名或密码不正确']);
                    /*dd(request()->all());*/
                }
            }else{
                //验证码不正确
                return back()->with(['status'=>'error','msg'=>'验证码不正确']);
            }
            }else{
            //首页
            return view('Home.login');
        }

    }

    //前台验证码
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


    //用户注册
    public function reg(){
        if(request()->isMethod('POST')){
            //存入缓存
            request()->flash();
            //注册用户业务逻辑
            $data['username'] = trim(request('username'));
            $data['password']=trim(md5(request('password')));
            $data['add_time']=time();
            //让短信验证码失效
            session()->forget('msg_code');
            if(!Member::where('username',$data['username'])->first() == $data['username'] && Member::insert($data) ){
                return back()->with(['status'=>'ok','msg'=>'注册成功','url'=>route('login')]);
            }else{
                return back()->with(['status'=>'error','msg'=>'用户名已存在']);
            }
        }else{
            return view('Home.reg');
        }
    }
    //发送短信验证码
    public function sendMsg(){
        //短信接口地址
        //$target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $target = config('app.msg.ApiUrl');

        //获取手机号
        $mobile = $_POST['mobile'];
        //生成的随机数
        $msg_code = random(4,1);
        if(empty($mobile)){
            echo '手机号码不能为空';
        }
        //APIId
        $apiId=config('app.msg.ApiId');
        //APIKey
        $apikey=config('app.msg.ApiKey');

        $post_data =
            "account=".$apiId."&password=".$apikey."&mobile="
            .$mobile."&content="
            .rawurlencode("您的验证码是：".$msg_code."。请不要把验证码泄露给其他人。");
        //用户名是登录ihuyi.com账号名（例如：cf_demo123）
        //查看密码请登录用户中心->验证码、通知短信->帐户及签名设置->APIKEY
        //发送短信并返回结果
        $gets =  xml_to_array(Post($post_data, $target));
        if($gets['SubmitResult']['code']==2){
            //session()->put('mobile',$mobile);
            session()->put('msg_code',$msg_code); //将短信验证码写入Session方便以后验证
        }
        echo $gets['SubmitResult']['msg']; //提交成功
    }

    //手机验证码验证
    public function chkMsg(){
        $msg_code=trim(request('msg_code'));
        if(session()->get('msg_code')==$msg_code){
            echo 'true';
        }else{
            echo 'false';
        }
    }
}