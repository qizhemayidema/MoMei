<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 9:33
 */
declare (strict_types = 1);

namespace app\aAdmin\controller;

use app\BaseController;
use app\common\service\AUser;
use app\common\service\Role;
use app\common\typeCode\aUser\Ying;
use app\Request;
use think\Validate;
use app\common\tool\Session;

class Login extends BaseController
{
    public function index()
    {
        return view();
    }

    public function check(Request $request)
    {
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
                if (!$result)  throw new \Exception($validate->getError());

                //查询登录的用户是否存在
                $res = (new AUser(new Ying()))->existsUsernameReturnInfo($data['username']);

                if (!$res)  throw new \Exception('用户不存在');

                if($res['status']==2) throw new \Exception('该账号已冻结');

                $verifyResult = (new AUser(new Ying()))->verifyAccount($data['username'],$data['password'],$res['slat']);

                if(!$verifyResult) throw new \Exception('账号或密码不正确');

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