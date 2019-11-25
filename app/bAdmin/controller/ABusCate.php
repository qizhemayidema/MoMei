<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Category;
use think\exception\ValidateException;
use think\facade\Validate;
use think\facade\View;
use think\Request;

class ABusCate extends Base
{
    public function index()
    {
        try{
            $data = (new Category())->getABusList();

            View::assign('cate',$data);

            return view('a_bus_cate/index');

        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function add()
    {
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

            (new Category())->insert((new \app\common\typeCode\cate\ABus()),$post);

            return json(['code'=>1,'msg'=>'success']);

        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
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

            (new Category())->update((new \app\common\typeCode\cate\ABus()),$post);

            return json(['code'=>1,'msg'=>'success']);

        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $cate_id = $request->post('cate_id');

        (new Category())->delete((new \app\common\typeCode\cate\ABus()),$cate_id);

        return json(['code'=>1,'msg'=>'success']);
    }
}
