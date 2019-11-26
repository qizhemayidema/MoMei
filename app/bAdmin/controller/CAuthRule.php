<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/26
 * Time: 11:32
 */

namespace app\bAdmin\controller;

use app\common\service\Permission;
use app\common\typeCode\permission\C as TypeDesc;
use app\Request;
use think\facade\View;
use think\Validate;
class CAuthRule extends Base
{
    public function index()
    {
        try{
            //查询院线的全部的权限
            $ruleArr = (new Permission())->getRuleList(new TypeDesc());
            View::assign('ruleArr',$ruleArr);
            return view('c_auth_rule/index');
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function add()
    {
        //查询院线的全部的权限
        $ruleArr = (new Permission())->getRuleList(new TypeDesc());
        View::assign('ruleArr',$ruleArr);
        return view('c_auth_rule/add');
    }

    public function save(Request $request)
    {
        $data =$request->post();
        try{
            $valiatde = new Validate();
            $rules = Array(
                'p_id|所属权限类目'=>'require',
                'name|权限名称'=>'require|max:60',
                'controller|控制器'=>'require|max:120',
                'action|方法'=>'require|max:60',
                '__token__'     => 'token',
            );
            $valiatde->rule($rules);
            $checkres  = $valiatde->check($data);
            if(!$checkres){
                throw new \Exception($valiatde->getError());
            }

            $res = (new Permission())->insert(new TypeDesc(),$data);
            if(!$res) throw new \Exception('添加失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        //查询修改的默认
        $id = $request->param('rule_id');
        $res = (new Permission())->getFindRes($id);
        View::assign('data',$res);

        //查询院线的全部的权限
        $ruleArr = (new Permission())->getRuleList(new TypeDesc());
        View::assign('ruleArr',$ruleArr);

        return view('c_auth_rule/edit');
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
            $res = (new Permission())->updataRes(new TypeDesc(),$post);
            if(!$res) throw new \Exception('修改失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $rule_id = $request->post('rule_id');

        $res = (new Permission())->delete((new TypeDesc()),$rule_id);
        if(!$res)  return json(['code'=>0,'msg'=>'删除失败']);

        return json(['code'=>1,'msg'=>'success']);
    }
}