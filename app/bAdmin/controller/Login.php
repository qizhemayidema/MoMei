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
use app\common\model\Manager;
use app\common\service\Role;
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

                //查询登录的用户
                $res = (new Manager())->where(['username'=>$data['username'],'password'=>md5($data['password'])])->find();
                if (!$res){
                    return json(['code' => 0, 'msg'=>'账号或密码不正确']);
                }
                if ($res['role_id']){
                    $res['role_name'] = (new Role())->getFindRes($res['role_id'])['role_name'];
                }else{
                    $res['role_name'] = '超级管理员';
                }
                //登陆成功
                \think\facade\Session::set('bAdmin_admin',$res);

                return json(['code' => 1, 'msg'=>'success']);

            }
        }
        catch (\Exception $e){
            return json(['code' => 0, 'msg'=>$e->getMessage()]);
        }
    }

    public function logout()
    {
        \think\facade\Session::set('bAdmin_admin',null);
        return redirect('index');
    }

}