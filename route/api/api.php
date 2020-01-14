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
        //获取产品详情
        Route::get('details','Product/details');
    });

    //影院相关
    Route::group('cinema',function(){
        //获取影院列表
        Route::get('/','Cinema/getList');
        //获取影院筛选条件
        Route::get('condition','Cinema/getCondition');
        //info
        Route::get('info','Cinema/getInfo');
        //获取某个影院的产品分类
        Route::get('cate','Cinema/getProductCate');
        //根据影院的产品分类获得的产品
        Route::get('product','Cinema/getProduct');
    });

    //院线 影投相关
    Route::group('theatreChain',function (){
        //获取影院列表
        Route::get('/','TheatreChain/getList');
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
        //信息
        Route::group('info',function (){
            //修改用户的头像 昵称
            Route::put('basics','User/basics');
        });
        //认证
        Route::group('auth',function(){
            //获取个人认证证件类型
            Route::get('licenseCate','User/getLicenseCate');
            //获取公司认证公司性质类型
            Route::get('licensePropertyCate','User/getLicensePropertyCate');
            //认证
            Route::post('/','User/setAuth');
        });
        //购物车
        Route::group('shopping',function(){
            //获取详情
            Route::get('info','Shopping/getInfo');
            //添加商品到购物车
            Route::post('/','Shopping/add');
            //从购物车删除商品
            Route::delete('/','Shopping/delete');
            //获取购物车列表
            Route::get('/','Shopping/getList');
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