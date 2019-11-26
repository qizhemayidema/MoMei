<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/25
 * Time: 14:22
 */
namespace app\bAdmin\controller;

use app\BaseController;
use app\common\model\Manager;
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
                $rules = [
                    'username'  => 'require',
                    'password'  => 'require',
                    'captcha'   => 'require|captcha',
                ];
                $messages = [
                    'username.require'      => '用户名必须填写',
                    'password.require'      => '密码必须填写',
                    'captcha.require'       => '验证码必须填写',
                    'captcha.captcha'       => '验证码不正确',
                ];
                $validate = new Validate($rules,$messages);
                $result = $validate->check($data);
                if (!$result) {
                    return json(['code' => 0, 'msg'=>$validate->getError()], 256);
                }

                //查询登录的用户
                $res = (new Manager())->where(['username'=>$data['username'],'password'=>md5($data['password'])])->find();
                if (!$res){
                    return json(['code' => 0, 'msg'=>'账号或密码不正确'], 256);
                }
                //登陆成功
                \think\facade\Session::set('admin',$res);

                return json(['code' => 1, 'msg'=>'success'], 256);

            }
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}