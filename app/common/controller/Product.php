<?php
declare (strict_types = 1);

namespace app\common\controller;

use app\common\typeCode\ManagerImpl;
use think\Request;

use app\common\service\CinemaProduct;
use app\common\service\CinemaScreen;
use think\facade\View;
use app\common\service\Category as CateService;


class Product
{
    //展示某些影院的产品列表
    public function getShowListPage($aCinemaId = null)      //如果传入aCinemaId 则表明只看该影院id的产品 否则是全部影院的产品
    {
        $request = \request();
        /*筛选条件*/
        // 产品分类
        $cateId = $request->param('cate_id') ?? 0;
        $cinemaId = $aCinemaId ?? $request->param('cinema_id') ?? 0;
        $screenId = ($cinemaId && $request->param('screen_id')) ? $request->param('screen_id') : 0;


        $service = new CinemaProduct($cinemaId);
        // 地区

        // 影院列表
        if (!$aCinemaId){
            $cinema = (new \app\common\service\Cinema(new \app\common\typeCode\manager\Cinema()))->setTypes(true)->getList();
        }else{
            $cinema = [];
        }

        //获取影厅
        $screen = (new CinemaScreen())->getList($cinemaId);

        //获取所有分类
        $cate = (new CateService())->getList((new \app\common\typeCode\cate\Product()));


        $list = $service->setShowType(true)->getEntityList(15,$cateId,$screenId);

        View::assign(compact('list','cate','cateId','cinema','cinemaId','screen','screenId'));


        return view('common@product/show_list');
    }
}
