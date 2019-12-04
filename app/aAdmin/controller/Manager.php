<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/3
 * Time: 21:15
 */

namespace app\aAdmin\controller;

use app\common\service\Role;
use app\common\service\Manager as ManagerService;
use app\common\typeCode\role\A as TypeDesc;
use app\common\tool\Session;
use app\common\typeCode\manager\Ying;
use app\common\typeCode\manager\Yuan;
use app\Request;
use think\facade\View;
use think\Validate;

class Manager extends Base
{
    public function index()
    {
        try{
            $info = (new Session())->getData();

            $managerService = '';
            if($info['type']==2){  //院线
                $managerService = new ManagerService(new Yuan());
            }elseif ($info['type']==3){  //影投
                $managerService = new ManagerService(new Ying());
            }

            if($info['group_code']) $managerService = $managerService->setGroupCode($info['group_code']);

            $userData = $managerService->showType(true)->pageLength()->getList();

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

        $model = new \app\common\model\Manager();
        try{
            $model->startTrans();

            if(!$checkRes)  throw new \Exception($validate->getError());

            $info = (new Session())->getData();

            $post['group_code'] = $info['group_code'];

            $managerService = '';

            if($info['type']==2){  //院线
                $managerService = new ManagerService(new Yuan());
            }elseif ($info['type']==3){  //影投
                $managerService = new ManagerService(new Ying());
            }

            $role = (new Role())->getFindRes($post['role_id']); //根据角色id查询出角色名称

            $post['role_name'] = $role['role_name'];

            $managerInfo = $managerService->existsUsername($post['username']);

            if(!empty($managerInfo)) throw new \Exception('用户名已存在');

            $insertResult = $managerService->insert($post);

            if(!$insertResult) throw new \Exception('添加失败');

            $model->commit();

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            $model->rollback();

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

        $model = new \app\common\model\Manager();

        try{
            $model->startTrans();

            if(!$checkRes)  throw new \Exception($validate->getError());

            $info = (new Session())->getData();

            $managerService = '';

            if($info['type']==2){  //院线
                $managerService = new ManagerService(new Yuan());
            }elseif ($info['type']==3){  //影投
                $managerService = new ManagerService(new Ying());
            }

            $managerInfo = $managerService->existsUsername($post['username'],$post['id']);

            if(!empty($managerInfo)) throw new \Exception('用户名已存在');

            $role = (new Role())->getFindRes($post['role_id']); //根据角色id查询出角色名称

            $post['role_name'] = $role['role_name'];

            $managerService->update($post['id'],$post);

            $model->commit();

            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            $model->rollback();

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

    public function changeStatus(Request $request)
    {
        $status = $request->post('status');

        $id = $request->post('id');

        (new ManagerService())->changeStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);
    }
}
