<?php

use think\facade\Route;

Route::group('/',function(){
    Route::group('area',function(){
        //获取热门城市
        Route::get('hot','Area/getHot');
        //查询地区筛选条件
        Route::get('condition','Area/getCondition');
    });

    Route::group('product',function(){
        //获取产品分类
        Route::get('cate','Product/getCate');
    });

    Route::group('cinema',function(){
        //获取影院筛选条件
        Route::get('condition','Cinema/getCondition');

    });

    Route::group('search',function(){
        //获取影院筛选条件
        Route::get('/','Search/index');

    });
});