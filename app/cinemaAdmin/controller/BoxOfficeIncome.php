<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/6
 * Time: 10:18
 */

namespace app\cinemaAdmin\controller;


use app\common\service\BoxOffice;
use app\common\tool\Session;
use app\common\service\Manager as ManagerService;
use think\Validate;
use think\facade\View;
use app\Request;

class BoxOfficeIncome extends  Base
{
    public function index()
    {
        $service = new BoxOffice();

        $info = (new Session())->getData();

        $data = (new BoxOffice())->setWhere('cinema_id',$info['group_code'])->order('create_time','desc')->pageLength(15)->getList();

        $all_sum = $service->getSum('cinema',$info['group_code']);

        View::assign('data',$data);
        View::assign('all_sum',$all_sum);

        return view();
    }

    public function add()
    {
        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $validate = new Validate();
        $rules = Array(
            'income_value|票房收入'=>'require|max:64',
            'number_value|观影人数'=>'require|max:64',
            '__token__'     => 'token',
        );
        $validate->rule($rules);
        $checkResult  = $validate->check($post);
        try{
            if(!$checkResult) throw new \Exception($validate->getError());

            $info = (new Session())->getData();


            $BoxOffice = new BoxOffice();

            $todayResult = $BoxOffice->getToday($info['group_code']);

            if(!empty($todayResult)) throw new \Exception('今日已经添加');

            $managerInfo = (new ManagerService())->getInfo($info['info_id']);

            $post['group_code'] = $info['group_code'];

            $post['cinema_name'] = $managerInfo['name'];

            $post['tou_id'] = $managerInfo['tou_id'];

            $post['yuan_id'] = $managerInfo['yuan_id'];

            $result = $BoxOffice->insert($post);

            if(!$result) throw new \Exception('添加失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');

        $data = (new BoxOffice())->get($id);

        View::assign('data',$data);

        return view();
    }

    public function update(Request $request){
        $post =$request->post();

        $validate = new Validate();
        $rules = Array(
            'id'=>'require',
            'income_value|票房收入'=>'require|max:64',
            'number_value|观影人数'=>'require|max:64',
            '__token__'     => 'token',
        );
        $validate->rule($rules);
        $checkResult  = $validate->check($post);
        try{
            if(!$checkResult) throw new \Exception($validate->getError());

            $result = (new BoxOffice())->update($post);

            if(!$request) throw new \Exception('编辑失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'mag'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try{
            $id = $request->post('id');

            $result = (new BoxOffice())->delete($id);

            if(!$result) throw new \Exception('删除失败');

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'mag'=>$e->getMessage()]);

        }
    }

}