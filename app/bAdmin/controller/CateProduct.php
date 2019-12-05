<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Category;
use app\common\typeCode\cate\Product as TypeDesc;
use think\facade\Validate;
use think\facade\View;
use think\Request;

class CateProduct extends Base
{
    public function index()
    {
        try{

            $data = (new Category())->getList((new TypeDesc()));

            View::assign('cate',$data);

            return view();

        }catch(\Exception $e){

            return $e->getMessage();
        }
    }

    public function add()
    {
        $list = (new Category())->getListByPId((new \app\common\typeCode\cate\Product()),0);

        View::assign('list',$list);

        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        try{
            $validate = Validate::rule([
                'name|分类名称'  => 'require|max:60',
                'order_num|排序' => 'require|between:0,999',
                '__token__'     => 'token',
            ]);

            if (!$validate->check($post)) throw new \Exception($validate->getError());

           $cateId = (new Category())->insert((new TypeDesc()),$post);

            //创建对应的产品规则初始数据
            (new \app\common\service\ProductRule())->insert(['cate_id'=>$cateId]);

            return json(['code'=>1,'msg'=>'success']);

        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $list = (new Category())->getListByPId((new \app\common\typeCode\cate\Product()),0);

        View::assign('list',$list);

        $id = $request->param('cate_id');


        View::assign('cate',(new Category())->getOneById($id));

        return view();

    }

    public function update(Request $request)
    {
        $post = $request->post();

        try{
            $validate = Validate::rule([
                'id'       => 'require',
                'name|分类名称'  => 'require|max:60',
                'order_num|排序' => 'require|between:0,999',
                '__token__'     => 'token',
            ]);

            if (!$validate->check($post)) throw new \Exception($validate->getError());

            (new Category())->update((new TypeDesc()),$post);

            return json(['code'=>1,'msg'=>'success']);

        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $cate_id = $request->post('cate_id');

        (new Category())->delete((new TypeDesc()),$cate_id);

        return json(['code'=>1,'msg'=>'success']);
    }
}
