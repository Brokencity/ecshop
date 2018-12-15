<?php
/**
 * 前台路由
 */

Route::group(['namespace'=>'Home'],function(){

    Route::any('/',['as'=>'/','uses'=>'IndexController@index']);//首页
    Route::any('login',['as'=>'login','uses'=>'LoginController@login']);//用户登陆
    Route::any('reg',['as'=>'reg','uses'=>'LoginController@reg']);//用户注册
    Route::any('captcha',['as'=>'captcha','uses'=>'LoginController@captcha']);//用户注册验证码
    Route::post('sendMsg',['as'=>'sendMsg','uses'=>'LoginController@sendMsg']);//发送短信验证码请求
    Route::post('chkMsg',['as'=>'chkMsg','uses'=>'LoginController@chkMsg']);//检验短信是否正确

    Route::any('logout',['as'=>'logout','uses'=>'IndexController@logout']);//用户退出


    Route::any('brand',['as'=>'brand','uses'=>'IndexController@brand']);//品牌文化页
    Route::any('star',['as'=>'pinpai/star','uses'=>'IndexController@star']);//知名明星
    Route::any('idea',['as'=>'pinpai/idea','uses'=>'IndexController@idea']);//品牌理念
    Route::any('store',['as'=>'pinpai/store','uses'=>'IndexController@store']);//珠宝店铺
    Route::any('love',['as'=>'pinpai/love','uses'=>'IndexController@love']);//真爱验证
    Route::any('verify',['as'=>'pinpai/verify','uses'=>'IndexController@verify']);//相守一生
    Route::any('deal',['as'=>'pinpai/deal','uses'=>'IndexController@deal']);//真爱协议
    Route::any('engrave',['as'=>'pinpai/engrave','uses'=>'IndexController@engrave']);//工匠雕刻

    //前台商品展示页面
    Route::any('Forever',['as'=>'Forever','uses'=>'SeriveController@Forever']);//Forever系列

    Route::any('detail/{id}',['as'=>'detail','uses'=>'IndexController@detail']); //商品详情页

    Route::any('help',['as'=>'help','uses'=>'IndexController@help']);  //帮助页面
    Route::any('help_c',['as'=>'help_c','uses'=>'SeriveController@help_c']);  //帮助页面
    Route::any('help_b',['as'=>'help_b','uses'=>'SeriveController@help_b']);  //帮助页面
    //Route::any('cart',['as'=>'list_all','uses'=>'SeriveController@list_all']);  //帮助页面

    Route::any('question',['as'=>'question','uses'=>'IndexController@question']);  //常见问题

    Route::any('active',['as'=>'active','uses'=>'IndexController@active']);   //最新活动
    Route::any('forget',['as'=>'forget','uses'=>'IndexController@forget']); //会员密码找回

    //搜索模块
    Route::get('search',['as'=>'search','uses'=>'SearchController@index']);

    //前台购物车模块
    Route::group(['prefix'=>'cart'],function()
    {
        Route::any('cart',['as'=>'cart/cart','uses'=>'CartController@cart']);      //购物车
        Route::any('toCart/{gid}',['as'=>'cart/toCart','uses'=>'CartController@toCart']); //加入购物车
        Route::any('lists',['as'=>'cart/lists','uses'=>'CartController@lists']);//商品展示页
        Route::any('cartOrder',['as'=>'cart/cartOrder','uses'=>'CartController@cartOrder']); //购物车订单详情
        Route::any('cartAgreement',['as'=>'cart/cartAgreement','uses'=>'CartController@cartAgreement']);//购物车真爱协议页
        Route::any('cartOrderSuccess',['as'=>'cart/cartOrderSuccess','uses'=>'CartController@CartOrderSuccess']);//订单生成页
        Route::any('cartDelete/{id}',['as'=>'cart/cartDelete','uses'=>'CartController@cartDelete']); //删除购物车单件商品
        Route::any('cartDeletes',['as'=>'cart/cartDeletes','uses'=>'CartController@cartDeletes']); //删除购物车多件商品
        Route::any('cartActive',['as'=>'cart/cartActive','uses'=>'CartController@cartActive']);  //提交真爱协议时改变选中商品状态
        Route::any('buyNow/{gid}',['as'=>'cart/buyNow','uses'=>'CartController@buyNow']); //立即购买
    });

    //前台会员中心模块
    Route::group(['prefix'=>'member'],function()
    {
        Route::any('member_index',['as'=>'member/member_index','uses'=>'MemberController@member_index']);//前台个人中心首页
        Route::any('member_addr',['as'=>'member/member_addr','uses'=>'MemberController@member_addr']); //会员收货地址
        Route::any('member_avatar',['as'=>'member/member_avatar','uses'=>'MemberController@member_avatar']);//会员头像上传
        Route::any('member_collect',['as'=>'member/member_collect','uses'=>'MemberController@member_collect']);//会员收藏
        Route::any('member_info',['as'=>'member/member_info','uses'=>'MemberController@member_info']); //会员资料修改
        Route::any('member_order',['as'=>'member/member_order','uses'=>'MemberController@member_order']);//会员订单
        Route::any('member_order_detail/{id}',['as'=>'member/member_order_detail','uses'=>'MemberController@member_order_detail']); //会员订单详情
        Route::any('member_pwd',['as'=>'member/member_pwd','uses'=>'MemberController@member_pwd']); //会员修改密码
        Route::any('member_editAdd/{id?}',['as'=>'member/member_editAdd','uses'=>'MemberController@member_editAdd']);//修改收货地址
        Route::get('member_address/{id?}/{active?}',['as'=>'member/member_address','uses'=>'MemberController@member_address']);//修改默认收货地址
        Route::get('member/delete_address/{id?}',['as'=>'member/delete_address','uses'=>'MemberController@delete_address']);//删除会员收货地址
    });

    //前台商品
    Route::group(['prefix'=>'goods'],function()
    {
        Route::any('goods_collect/{id}',['as'=>'goods/goods_collect','uses'=>'GoodsController@goods_collect']);//商品收藏
    });

});