<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\common\tool\Session;
use think\facade\Validate;
use app\common\service\CinemaScreen as Service;
use app\common\service\CinemaProduct as CinemaProductServer;
use app\common\service\UserShopping as UserShoppingServer;
use think\facade\View;
use think\Request;

class Screen extends Base
{
    public function index()
    {
        $user = (new Session())->getData();

        $cinema_id = $user['group_code'];


        $list = (new Service())->getList($cinema_id,15);

        View::assign('list',$list);
        return view();
    }

    public function add()
    {
        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $validate = Validate::rule([
            'name|影厅名称' => 'require|max:32',
            'seat_sum|座位数量' => 'require|integer|between:0,999',
            'sort|排序' => 'require|between:0,999',
            '__token__' => 'token',
        ]);


        try{

           if (!$validate->check($post)) throw new \Exception($validate->getError());


            $user = (new Session())->getData();

            $post['cinema_id'] = $user['group_code'];

            (new Service())->insert($post);

        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');

        $data = (new Service())->get($id);

        View::assign('data',$data);

        return view();
    }

    public function update(Request $request)
    {
        $post = $request->post();

        $validate = Validate::rule([
            'id'           => 'require',
            'name|影厅名称' => 'require|max:32',
            'seat_sum|座位数量' => 'require|integer|between:0,999',
            'sort|排序' => 'require|between:0,999',
            '__token__' => 'token',
        ]);

        //先查询出此id原来的数据  若果修改了影厅名称那么联动的产品表,购物车表中存储的影厅名成也需要跟着变化
        $data = (new Service())->get($post['id']);

        try{

            if (!$validate->check($post)) throw new \Exception($validate->getError());


            $user = (new Session())->getData();

            $post['cinema_id'] = $user['group_code'];

            (new Service())->update($post['id'],$post);

            if($data['name']!=$post['name']){  //这里是修改了影厅名称 修改购物车表和产品表
                $screenNameResult = (new CinemaProductServer())->updateByScreenId($post['id'],$post['name']);  //产品表
                $shoppingResult = (new UserShoppingServer())->updateByScreenId($post['id'],$post['name']);  //购物车表
            }

        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function delete(Request $request)
    {
        $id = $request->param('id');

        $user = (new Session())->getData();

        $cinemaId = $user['group_code'];

        (new Service())->delete($id,$cinemaId);

        return json(['code'=>1,'msg'=>'success']);
    }
}
