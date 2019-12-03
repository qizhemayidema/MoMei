<?php
declare (strict_types=1);

namespace app\cinemaAdmin\controller;

use app\BaseController;
use app\common\service\Cinema as CinemaService;
use app\common\service\Role;
use app\common\tool\Session;
use app\Request;
use think\Validate;

class Login extends BaseController
{
    public function index()
    {
        return view();
    }

    public function check(Request $request)
    {
        try {
            if (request()->isPost()) {
                $data = $request->post();

                $validate = new Validate();
                $rules = [
                    'username|用户名' => 'require',
                    'password|密码' => 'require',
                    'captcha|验证码' => 'require|captcha',
                ];
                $validate->rule($rules);
                $result = $validate->check($data);
                if (!$result) {
                    return json(['code' => 0, 'msg' => $validate->getError()]);
                }

                $service = new CinemaService();
                //查询登录的用户
                $res = $service->existsUsername($data['username']);
                if (!$res && !$service->verifyAccount($data['username'],$data['password'],$res['slat'])) {
                    return json(['code' => 0, 'msg' => '账号或密码不正确']);
                }
                if ($res['role_id']) {
                    $res['role_name'] = (new Role())->getFindRes($res['role_id'])['role_name'];
                } else {
                    $res['role_name'] = '超级管理员';
                }
                //登陆成功
                (new Session())->setData($res);

                return json(['code' => 1, 'msg' => 'success']);

            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        (new Session())->setData(NULL);
        return redirect('index');
    }

}