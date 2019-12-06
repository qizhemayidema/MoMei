<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/25
 * Time: 14:22
 */
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\common\service\Manager as ManagerService;
use app\common\typeCode\manager\B as TypeDesc;
use app\common\service\Role;
use app\common\tool\Session;
use app\common\typeCode\manager\B;
use app\Request;
use think\Validate;
class Login extends BaseController
{
    public function index(){
        return view('login/index');
    }

    public function check(Request $request){
        try{
            if(request()->isPost()){
                $data =$request->post();

                $validate = new Validate();
                $rules = [
                    'username|用户名'  => 'require',
                    'password|密码'  => 'require',
                    'captcha|验证码'   => 'require|captcha',
                ];
                $validate->rule($rules);
                $result = $validate->check($data);
                if (!$result) {
                    return json(['code' => 0, 'msg'=>$validate->getError()]);
                }

                $res = (new ManagerService(new TypeDesc()))->existsUsername($data['username']);  //查询用户是否存在

                if (!$res)  throw new \Exception('账号不存在');

                if ($res['delete_time']!=0) throw new \Exception('账号已被回收');

                if ($res['status']==2) throw new \Exception('账号已被冻结');

                if(md5($data['password'].$res['salt']) != $res['password'] )   throw new \Exception('密码错误');

                if ($res['type'] != (new B())->getManagerType()) throw new \Exception('账号或密码错误');

                if ($res['role_id']){
                    $res['role_name'] = (new Role())->getFindRes($res['role_id'])['role_name'];
                }else{
                    $res['role_name'] = '超级管理员';
                }
                //登陆成功
                (new Session())->setData($res);

                return json(['code' => 1, 'msg'=>'success']);

            }
        }
        catch (\Exception $e){
            return json(['code' => 0, 'msg'=>$e->getMessage()]);
        }
    }

    public function logout()
    {
        (new Session())->setData(NULL);
        return redirect('index');
    }

}