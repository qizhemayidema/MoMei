<?php

use think\facade\Route;

Route::group('/',function(){

    //地区相关
    Route::group('area',function(){
        //获取热门城市
        Route::get('hot','Area/getHot');
        //查询地区筛选条件
        Route::get('condition','Area/getCondition');
    });

    //产品相关
    Route::group('product',function(){
        //获取产品分类
        Route::get('cate','Product/getCate');
    });

    //影院相关
    Route::group('cinema',function(){
        //获取影院筛选条件
        Route::get('condition','Cinema/getCondition');
        //info
        Route::get('info','Cinema/getInfo');
        //获取某个影院的产品分类
        Route::get('cate','Cinema/getProductCate');
    });

    //搜索相关
    Route::group('search',function(){
        //获取影院筛选条件
        Route::get('/','Search/index');

    });

    //文章相关
    Route::group('article',function(){
        //获取
        Route::get('cate','Article/getCate');
    });

    //sms相关
    Route::group('sms',function(){
        //发送验证码
        Route::get('captcha','User/getPhoneCode');
    });

    //用户相关
    Route::group('user',function(){
        //注册
        Route::post('/','User/register');
        //认证
        Route::post('auth','User/auth');
        //购物车
        Route::group('shopping',function(){

            Route::post('/','Shopping/add');
        });
    });


    Route::post('login','User/login');


    Route::group('upload',function(){
        //上传base64图片
        Route::post('base64Img','Upload/base64Img');
        //上传二进制图片
        Route::post('streamImg','Upload/streamImg');
    });
});