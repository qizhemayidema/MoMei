<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 14:23
 */

namespace app\bAdmin\controller;

use \app\common\typeCode\NewsCategory\NewsCateGory as CateDesc;
use app\Request;
use think\facade\View;
use think\Validate;

class NewsCategory extends Base
{
    public function index()
    {
        try{
            $data = (new \app\common\service\NewsCategory())->getNewsCategoryLists(new CateDesc(),true,3);

            View::assign('data',$data);

            return View();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function add()
    {
        try{
            $data = (new \app\common\service\NewsCategory())->getListByPId();

            View::assign('data',$data);

            return View();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function save(Request $request)
    {
        try{
            $post = $request->post();

            $validate = new Validate();
            $rules = [
                'pid|所属类别'=>'require',
                'type|类型'=>'require',
                'name|类别名称'=>'require|max:60',
                'order_num|排序'=>'require|between:0,999',
                '__token__'=>'token',
            ];
            $validate->rule($rules);

            if(!$validate->check($post)) throw new \Exception($validate->getError());

            $result = (new \app\common\service\NewsCategory())->insert((new CateDesc()), $post);

            if(!$result) throw new \Exception('添加失败');

            return json(['code'=>1,'msg'=>'添加成']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        try{
            $cateId = $request->get('cate_id');

            $findCateResult = (new \app\common\service\NewsCategory())->getFindRes($cateId);

            $data = (new \app\common\service\NewsCategory())->getNewsCategoryLists(new CateDesc(),true);

            View::assign('data',$data);

            View::assign('findCateResult',$findCateResult);

            return View();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function update(Request $request)
    {
        try{
            $post = $request->post();

            $validate = new Validate();
            $rules = [
                'id'=>'require',
                'pid|所属类别'=>'require',
                'type|类型'=>'require',
                'name|类别名称'=>'require|max:60',
                'order_num|排序'=>'require|between:0,999',
                '__token__'=>'token',
            ];

            $validate->rule($rules);

            if(!$validate->check($post)) throw new \Exception($validate->getError());

            $productList = [];

            if($post['type']==1) $productList = (new \app\common\service\NewsProduct())->getProductList($post['id']);

            if(count($productList)>1) throw new \Exception('该类别中有多条新闻');

            $updateResult = (new \app\common\service\NewsCategory())->updateRes((new CateDesc()),$post);

            if(!$updateResult) throw new \Exception('修改失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try{
            $cateId =$request->post('cate_id');

            $result = (new \app\common\service\NewsCategory())->softDelete((new CateDesc()),$cateId);

            if(!$result) throw new \Exception('删除失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}