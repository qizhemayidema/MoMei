<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/27
 * Time: 16:31
 */

namespace app\bAdmin\controller;



use app\common\service\Role;
use app\common\typeCode\role\B as TypeDesc;
use app\common\service\Manager as ManagerService;
use app\common\typeCode\manager\B as BManagerTypeDesc;
use app\Request;
use think\facade\View;
use think\Validate;

class Manager extends Base
{
    public function index()
    {
        try{
            $managerService = new ManagerService(new BManagerTypeDesc());

            $userData = $managerService->getList();

            View::assign('userData',$userData);

            return view();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function add()
    {
        $roleData = (new Role())->getRoleList(new TypeDesc());

        View::assign('data',$roleData);

        return view();
    }

    public function save(Request $request)
    {
        try{
            $post = $request->post();
            $validate = new Validate();
            $rules = Array(
                'username|用户名'=>'require|max:30',
                'password|密码'=>'require|max:30|confirm:affirm_password',
                'affirm_password|确认密码'=>'require|max:30|confirm:password',
                'role_id|角色'=>'require',
                '__token__'     => 'token',
            );

            $validate->rule($rules);
            $checkRes  = $validate->check($post);
            if(!$checkRes)  throw new \Exception($validate->getError());

            $managerService = new ManagerService(new BManagerTypeDesc());

            $managerInfo = $managerService->existsUsername($post['username']);

            if(!empty($managerInfo)) throw new \Exception('用户名已存在');

            $insertResult = $managerService->insert($post);
            if(!$insertResult) throw new \Exception('添加失败');
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        try{
            $uid = $request->param('userid');

            $userData = (new ManagerService())->get($uid);

            $roleData = (new Role())->getRoleList(new TypeDesc());

            View::assign('data',$roleData);

            View::assign('userData',$userData);

            return view();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function update(Request $request)
    {
        try{
            $post = $request->post();
            $validate = new Validate();
            if(!empty($post['password'])){
                $rules = Array(
                    'id'=>'require',
                    'username|用户名'=>'require|max:30',
                    'password|密码'=>'require|max:30|confirm:affirm_password',
                    'affirm_password|确认密码'=>'require|max:30|confirm:password',
                    'role_id|角色'=>'require',
                    '__token__'     => 'token',
                );
            }else{
                $rules = Array(
                    'id'=>'require',
                    'username|用户名'=>'require|max:30',
                    'role_id|角色'=>'require',
                    '__token__'     => 'token',
                );
            }

            $validate->rule($rules);
            $checkRes  = $validate->check($post);
            if(!$checkRes)  throw new \Exception($validate->getError());

            $managerService = new ManagerService(new BManagerTypeDesc());

            $managerInfo = $managerService->existsUsername($post['username'],$post['id']);

            if(!empty($managerInfo)) throw new \Exception('用户名已存在');

            $managerService->update($post['id'],$post);

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try{
            $userId = $request->post('user_id');
            //先查询该用户
            (new ManagerService())->delete($userId);

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}