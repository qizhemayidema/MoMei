<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Category;
use app\common\typeCode\cate\Product;
use think\Request;
use think\facade\View;

class ProductRule
{
    public function index()
    {
        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();


    }

    public function edit(Request $request)
    {
        $id = $request->post('id');

        $list = (new \app\common\service\ProductRule())->getByCateId($id);

        View::assign('data',$list);

        View::assign('id',$id);

       return view();

    }

    public function getCateList()
    {
        $list = (new Category())->getList((new Product()));

        return json(['code'=>1,'data'=>$list]);
    }

}
