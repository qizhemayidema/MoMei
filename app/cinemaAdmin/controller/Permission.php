<?php

declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\common\service\Permission as Service;
use app\common\typeCode\permission\Cinema as TypeDesc;
use app\Request;
use think\facade\View;
use think\Validate;
class Permission extends Base
{
    public function index()
    {
        try{
            //查询影院的全部的权限
            $ruleArr = (new Service())->getRuleList(new TypeDesc());
            View::assign('ruleArr',$ruleArr);
            return view();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function add()
    {
        //查询影院的全部的权限
        $ruleArr = (new Service())->getRuleList(new TypeDesc());
        View::assign('ruleArr',$ruleArr);
        return view();
    }

    public function save(Request $request)
    {
        $data =$request->post();
        try{
            $validate = new Validate();
            $rules = Array(
                'p_id|所属权限类目'=>'require',
                'name|权限名称'=>'require|max:60',
                'controller|控制器'=>'require|max:120',
                'action|方法'=>'require|max:60',
                '__token__'     => 'token',
            );
            $validate->rule($rules);
            $checkResult  = $validate->check($data);
            if(!$checkResult){
                throw new \Exception($validate->getError());
            }

            $res = (new Service())->insert(new TypeDesc(),$data);
            if(!$res) throw new \Exception('添加失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $service = new Service();
        //查询修改的默认
        $id = $request->param('rule_id');
        $res = $service->getFindRes($id);
        View::assign('data',$res);

        //查询影院的全部的权限
        $ruleArr = $service->getRuleList(new TypeDesc());
        View::assign('ruleArr',$ruleArr);

        return view();
    }

    public function update(Request $request)
    {
        $post = $request->post();
        try{
            $valiatde = new Validate();
            $valiatde->rule(Array(
                'id'       => 'require',
                'p_id|所属权限类目'=>'require',
                'name|权限名称'=>'require|max:60',
                'controller|控制器'=>'require|max:120',
                'action|方法'=>'require|max:60',
                '__token__'     => 'token',
            ));
            if(!$valiatde->check($post))  throw new \Exception($valiatde->getError());
            $res = (new Service())->updataRes(new TypeDesc(),$post);
            if(!$res) throw new \Exception('修改失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $rule_id = $request->post('rule_id');

        $res = (new Service())->delete((new TypeDesc()),$rule_id);
        if(!$res)  return json(['code'=>0,'msg'=>'删除失败']);

        return json(['code'=>1,'msg'=>'success']);
    }
}