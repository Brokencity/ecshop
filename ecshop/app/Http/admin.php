<?php
/**
后台路由
 */

Route::group(['namespace'=>'Admin'],function(){
    //后台登录页
    Route::any('bg',['as'=>'bg','uses'=>'LoginController@login']);

    Route::group(['prefix'=>'bg'],function(){
        //后台验证码
        Route::any('captcha',['as'=>'bg/captcha','uses'=>'LoginController@captcha']);

        //后台中间件的判断
        Route::group(['middleware'=>'login'],function(){
            //后台退出
            Route::get('logout',['as'=>'bg/logout','uses'=>'LoginController@logout']);
            //后台首页
            Route::get('index',['as'=>'bg/index','uses'=>'IndexController@index']); //后台首页
            Route::get('top',['as'=>'bg/top','uses'=>'IndexController@top']); //顶部
            Route::get('left',['as'=>'bg/left','uses'=>'IndexController@left']); //左边
            Route::get('main',['as'=>'bg/main','uses'=>'IndexController@main']); //主页
            Route::get('footer',['as'=>'bg/footer','uses'=>'IndexController@footer']); //底部页面
        });
        //后台管理员模块
        Route::get('admin/adminList',['as'=>'bg/admin/adminList','uses'=>'AdminController@adminList']); //管理列表
        Route::any('admin/addAdmin',['as'=>'bg/admin/addAdmin','uses'=>'AdminController@addAdmin']); //添加管理员
        Route::any('admin/changePwd/{id}',['as'=>'bg/admin/changePwd','uses'=>'AdminController@changePwd']); //更改密码
        Route::any('admin/delete/{id}',['as'=>'bg/admin/delete','uses'=>'AdminController@delete']);  //删除管理员
        Route::any('admin/active/{id}/{active}',['as'=>'bg/admin/active','uses'=>'AdminController@active']);//更改状态
        Route::post('admin/multiDelete',['as'=>'bg/admin/multiDelete','uses'=>'AdminController@multiDelete']);//删除多条
        Route::post('admin/editAdminName',['as'=>'bg/admin/editAdminName','uses'=>'AdminController@editAdminName']);
        //商品分类模块
        Route::any('category/lists_cate',['as'=>'bg/category/lists_cate','uses'=>'ListController@lists_cate']); //商品分类列表
        Route::any('category/add_cate/{pid?}',['as'=>'bg/category/add_cate','uses'=>'ListController@add_cate']);//添加商品分类
        Route::any('category/getSubCate/{id}',['as'=>'bg/category/getSubCate','uses'=>'ListController@getSubCate']);//根据id查找子分类
        Route::any('category/active/{path}/{active}',['as'=>'bg/category/active','uses'=>'ListController@active']);//上下架状态显示
        Route::any('category/delete/{path}',['as'=>'bg/category/delete','uses'=>'ListController@delete']);//上下架状态显示
        Route::get('getGoodsTop5',['as'=>'bg/getGoodsTop5','uses'=>'ListController@getGoodsTop5']);//柱状图数据显示

        //商品分类添加
        //会员管理模块
        Route::get('member/memberList',['as'=>'bg/member/memberList','uses'=>'MemberListController@memberList']);//会员列表
        Route::any('member/delMember/{id}',['as'=>'bg/member/delMember','uses'=>'MemberListController@delMember']);//删除会员
        Route::any('member/active/{id}/{active}',['as'=>'bg/member/active','uses'=>'MemberListController@active']);//更改会员状态
        Route::any('member/allDelete',['as'=>'bg/member/allDelete','uses'=>'MemberListController@allDelete']);//删除选中数据



        //后台订单管理模块
        Route::any('order/orderList',['as'=>'bg/order/orderList','uses'=>'OrderController@orderList']);  //订单列表

    });

});