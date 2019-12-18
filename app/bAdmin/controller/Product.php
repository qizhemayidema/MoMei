<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\CinemaProduct;
use app\common\service\CinemaScreen;
use think\facade\View;
use think\Request;
use app\common\service\Category as CateService;

class Product extends Base
{
    public function index(Request $request)
    {
        /*筛选条件*/
        // 产品分类
        $cateId = $request->param('cate_id') ?? 0;
        $cinemaId = $request->param('cinema_id') ?? 0;
        $screenId = ($cinemaId && $request->param('screen_id')) ? $request->param('screen_id') : 0;


        $service = new CinemaProduct($cinemaId);
        // 地区

        // 影院列表
        $cinema = (new \app\common\service\Cinema((new \app\common\typeCode\manager\Cinema())))->setTypes(true)->getList();

        //获取影厅
        $screen = (new CinemaScreen())->getList($cinemaId);

        //获取所有分类
        $cate = (new CateService())->getList((new \app\common\typeCode\cate\Product()));


        $list = $service->setShowType(true)->getEntityList(15,$cateId,$screenId);

        View::assign(compact('list','cate','cateId','cinema','cinemaId','screen','screenId'));

        return view();

    }
}