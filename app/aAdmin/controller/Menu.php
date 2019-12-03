<?php
declare (strict_types = 1);

namespace app\aAdmin\controller;

use app\BaseController;
use app\common\typeCode\menu\A as TypeDesc;
use app\common\service\Menu as Service;
use think\facade\View;
use think\Request;
use think\Validate;

class Menu extends Base
{
    public function index()
    {
         $list = (new Service((new TypeDesc())))->getList();

         View::assign('list',$list);

         return view();

    }

    public function add()
    {
        $p = (new Service(new TypeDesc()))->getListForPId(0);

        View::assign('list',$p);

        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        try{
            $validate = new Validate();
            $rules = [
                'p_id|所属节点'=>'require|max:30',
                'title|标题'=>'require|max:30',
                'icon|标识'=>'max:30',
                'controller|控制器'=>'require|max:128',
                'action|方法'=>'require|max:64',
                '__token__'     => 'token',
            ];
            $validate->rule($rules);

            $checkers  = $validate->check($post);

            if(!$checkers) throw new \Exception($validate->getError());

            (new Service(new TypeDesc()))->insert($post);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');

        $service = (new Service(new TypeDesc()));

        $p = (new Service(new TypeDesc()))->getListForPId(0);

        $data = $service->get($id);

        View::assign('data',$data);

        View::assign('list',$p);

        return view();
    }

    public function update(Request $request)
    {
        $post = $request->post();

        try{
            $validate = new Validate();
            $rules = [
                'p_id|所属节点'=>'require|max:30',
                'title|标题'=>'require|max:30',
                'icon|标识'=>'max:30',
                'controller|控制器'=>'require|max:128',
                'action|方法'=>'require|max:64',
                '__token__'     => 'token',
            ];
            $validate->rule($rules);

            $checkers  = $validate->check($post);

            if(!$checkers) throw new \Exception($validate->getError());

            (new Service(new TypeDesc()))->update($post['id'],$post);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function delete(Request $request)
    {
        $id = $request->post();

        try{
            (new Service(new TypeDesc()))->delete($id);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }
}
