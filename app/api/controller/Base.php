<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\BaseController;
use think\Request;

class Base extends BaseController
{
    private $userInfo = [];

    private $token = '';

    public function initialize()
    {
        parent::initialize();

        $this->getUserInfo();
    }

    protected function getUserInfo()
    {

        if (\request()->isDelete()){
            $token = \request()->delete('token');
        }else if (\request()->isPut()){
            $token = \request()->put('token');
        }else{
            $token = \request()->param('token') ;
        }
        if ($token) {
            $this->token = $token;
            $this->userInfo = (new \app\common\model\User())->receptionShowData()->where(['token' => $token])->find();
        }
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        if ($name == 'userInfo') {
            if (!$this->$name) {
                if (\Request()->isGet()){
                    echo '请先登录账号~';
                }
                header('Content-type: application/json');
                exit(json_encode(['code' => 0, 'msg' => '请先登录账号~'], 256));

            }
            return $this->$name;
        }
    }

    //检查用户是否拥有互动的权限
    public function checkUserWriteAuth($user)
    {
        if ($user['license_status'] != 3) return ['code'=>0,'msg'=>'账号认证后才可继续操作'];

        return ['code' => 1];
    }
}
