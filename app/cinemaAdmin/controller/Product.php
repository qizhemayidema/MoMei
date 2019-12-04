<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\BaseController;
use app\common\service\Category;
use app\common\service\ProductRule as PRoleService;

use think\Request;

class Product extends Base
{
    public function index()
    {

    }

    public function add()
    {
        return view('add_index');
    }

    public function getFormHtml(Request $request)
    {
        $cateId = $request->param('id');


        $service = new PRoleService();

        $rule = $service->getByCateId($cateId);

        $level = $service->getLevelByProductId($rule['id']);

        View::assign('rule', $rule);

        View::assign('cate_id', $cateId);

        View::assign('level', $level);

        return view();
    }

    public function getCateList()
    {
        $list = (new Category())->getList((new \app\common\typeCode\cate\Product()));

        return json(['code' => 1, 'data' => $list]);

    }
}