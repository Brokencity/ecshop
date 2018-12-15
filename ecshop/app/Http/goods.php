<?php
//商品管理
Route::group(['namespace'=>'Admin'],function (){
    Route::group(['prefix'=>'bg'],function (){
        //商品管理模块
        Route::any('goods/addGoods',['as'=>'bg/goods/addGoods','uses'=>'GoodsController@addGoods']);//商品添加
        Route::any('goods/goodsList',['as'=>'bg/goods/goodsList','uses'=>'GoodsController@goodsList']);//商品列表
        Route::any('goods/addImg', ['as'=>'bg/goods/addImg','uses'=>'GoodsController@addImage']);//添加商品图片
        Route::any('goods/goodsActive/{id}/{active}', ['as'=>'bg/goods/goodsActive','uses'=>'GoodsController@goodsActive']);//修改商品状态
        Route::any('goods/goodsDelete/{id}', ['as'=>'bg/goods/goodsDelete','uses'=>'GoodsController@goodsDelete']);//根据id删除商品
        Route::any('goods/goodsDeletes', ['as'=>'bg/goods/goodsDeletes','uses'=>'GoodsController@goodsDeletes']);//批量删除商品
        Route::any('goods/goodsRedact/{id}', ['as'=>'bg/goods/goodsRedact','uses'=>'GoodsController@goodsRedact']);//编辑商品

        //根据pid异步获取子分类(三级联动中使用)
        Route::post('category/getCateByPid', ['as'=>'bg/category/getCateByPid','uses' => 'CategoryController@getCateByPid']);

    });

});
