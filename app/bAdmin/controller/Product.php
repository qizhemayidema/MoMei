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
        return (new \app\common\controller\Product())->getShowListPage();


    }

    public function info()
    {

    }
}