<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/29
 * Time: 9:22
 */

namespace app\bAdmin\controller;

use app\common\service\NewsProduct as NewsProductService;
use app\Request;
use think\facade\View;
use app\common\tool\Upload;
use think\Validate;

class NewsProduct extends Base
{
    public function index()
    {
        try{
            $data = (new NewsProductService())->getProductList(false,true,15);

            $cateData = (new \app\common\service\NewsCategory())->getNewsCategoryAllLists();

            View::assign('cateData',array_column($cateData,'name','id'));

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

            if(!isset($_FILES['pic'])) throw new \Exception('请先上传封面图');

            $picUrl = (new Upload())->uploadOnePic('newsProduct/','pic');

            if($picUrl['code']==0) throw new \Exception($picUrl['msg']);

            $post['pic'] = $picUrl['msg'];

            $validate = new Validate();
            $rules = [
                'cate_id|所属类别'=>'require',
                'title|新闻标题'=>'require|max:60',
                'content|内容'=>'require',
                'sort|排序'=>'require|between:0,999',
            ];
            $validate->rule($rules);
            if(!$validate->check($post)) throw new \Exception($validate->getError());

            $cateFind = (new \app\common\service\NewsCategory())->getFindRes($post['cate_id']);

            $productFind = [];

            if($cateFind['type']==1) $productFind = (new NewsProductService())->getProductByCateId($post['cate_id'],true);

            if(!empty($productFind)) throw new \Exception('所属类别已经有新闻');

            $insertResult = (new NewsProductService())->insertRes($post);

            if(!$insertResult) throw new \Exception('添加失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        try{
            $productId = $request->get('product_id');

            $cateData = (new \app\common\service\NewsCategory())->getListByPId();


            $data = (new NewsProductService())->getProductById($productId);

            if(empty($data)) throw new \Exception('该新闻不存在');

            View::assign('cateData',$cateData);

            View::assign('data',$data);

            return View();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function update(Request $request)
    {
        try{
            $post = $request->post();

            if(isset($_FILES['pic'])){
                $picUrl = (new Upload())->uploadOnePic('newsProduct/','pic');

                if($picUrl['code']==0) throw new \Exception($picUrl['msg']);

                $post['pic'] = $picUrl['msg'];
            }

            $validate = new Validate();
            $rules = [
                'id'=>'require',
                'cate_id|所属类别'=>'require',
                'title|新闻标题'=>'require|max:60',
                'content|内容'=>'require',
                'sort|排序'=>'require|between:0,999',
            ];
            $validate->rule($rules);
            if(!$validate->check($post)) throw new \Exception($validate->getError());

            $cateFind = (new \app\common\service\NewsCategory())->getFindRes($post['cate_id']);

            $productFind = [];

            if($cateFind['type']==1) $productFind = (new NewsProductService())->getProductByCateId($post['cate_id'],true);

            if(!empty($productFind)) throw new \Exception('所属类别已经有新闻');

            $updateResult =  (new NewsProductService())->updateRes($post);

            if(!$updateResult) throw new \Exception('修改失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request){
        try{
            $productId = $request->post('product_id');

            $result = (new NewsProductService())->softDelete($productId);

            if(!$result) return json('删除失败');

            return json(['code'=>1,'msg'=>'删除成功']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function uploadPic()
    {
        return json((new Upload())->uploadOnePic('newsProduct/'));
    }
}