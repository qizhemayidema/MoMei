<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/26
 * Time: 11:55
 */
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Permission;
use app\common\service\Role as Service;
use app\common\typeCode\role\B as TypeDesc;
use app\common\typeCode\permission\B as RuleTypeDesc;
use app\Request;
use think\facade\View;
use think\Validate;

class Role extends Base
{
    public function index()
    {
        try{
            //查询平台的全部权限组
            $roleList = (new Service())->getRoleList(new TypeDesc());

            View::assign('roleList',$roleList);
            return view();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function add()
    {
        //取出来所属的全部的权限
        $ruleArr = (new Permission())->getRuleList(new RuleTypeDesc());

        view::assign('ruleArr',$ruleArr);
        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        try{
            $valiatde = new Validate();
            $rules = Array(
                'role_name|角色名称'=>'require|max:30',
                'role_desc|角色备注'=>'require|max:120',
                'rules|权限'=>'require',
                '__token__'     => 'token',
            );
            $valiatde->rule($rules);
            $checkres  = $valiatde->check($post);
            if(!$checkres){
                throw new \Exception($valiatde->getError());
            }

            $res = (new Service())->insert(new TypeDesc(),false,$post);
            if(!$res) throw new \Exception('添加失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        //查出默认的
        $id = $request->param('role_id');
        $dataRes = (new Service())->getFindRes($id);
        $dataRes['permission_ids'] = explode(',',$dataRes['permission_ids']);
        view::assign('dataRes',$dataRes);

        //取出来所属的全部的权限
        $ruleArr = (new Permission())->getRuleList(new RuleTypeDesc());
        view::assign('ruleArr',$ruleArr);

        return View();
    }

    public function update(Request $request)
    {
        $post = $request->post();
        try{
            $valiatde = new Validate();
            $rules = Array(
                'id'=>'require',
                'role_name|角色名称'=>'require|max:30',
                'role_desc|角色备注'=>'require|max:120',
                'rules|权限'=>'require',
                '__token__'     => 'token',
            );
            $valiatde->rule($rules);
            $checkres  = $valiatde->check($post);
            if(!$checkres){
                throw new \Exception($valiatde->getError());
            }

            $res = (new Service())->updateRes(new TypeDesc(),$post);
            if(!$res) throw new \Exception('修改失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $roleId =  $request->post('role_id');
        try{
            $res = (new Service())->delete((new TypeDesc()),$roleId);
            if(!$res)  return json(['code'=>0,'msg'=>'删除失败']);
            return json(['code'=>1,'msg'=>'删除成功']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}