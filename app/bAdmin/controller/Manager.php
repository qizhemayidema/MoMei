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
use app\Request;
use think\facade\View;
use think\Validate;

class Manager extends Base
{
    public function index()
    {
        try{
            $userData = (new \app\common\service\Manager())->getManagerList(15);

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
            $valiatde = new Validate();
            $rules = Array(
                'username|用户名'=>'require|max:30',
                'password|密码'=>'require|max:30|confirm:affirm_password',
                'affirm_password|确认密码'=>'require|max:30|confirm:password',
                'role_id|角色'=>'require',
                '__token__'     => 'token',
            );

            $valiatde->rule($rules);
            $checkRes  = $valiatde->check($post);
            if(!$checkRes)  throw new \Exception($valiatde->getError());

            $managerInfo = (new \app\common\service\Manager())->getManagerByUsername($post['username']);
            if(!empty($managerInfo)) throw new \Exception('用户名已存在');

            $insertResult = (new \app\common\service\Manager())->insert($post);
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

            $userData = (new \app\common\service\Manager())->getFindData($uid);

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
            $valiatde = new Validate();
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

            $valiatde->rule($rules);
            $checkRes  = $valiatde->check($post);
            if(!$checkRes)  throw new \Exception($valiatde->getError());

            $managerInfo = (new \app\common\service\Manager())->getManagerByUsername($post['username']);
            if(!empty($managerInfo)) throw new \Exception('用户名已存在');

            $updateResult = (new \app\common\service\Manager())->updateRes($post);
            if(!$updateResult) throw new \Exception('修改失败');
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
            $deleteResult = (new \app\common\service\Manager())->delete($userId);
            if($deleteResult['code']==0) throw new \Exception($deleteResult['msg']);
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }
}