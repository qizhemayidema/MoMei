<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\common\service\Role;
use app\common\typeCode\role\B;
use app\middleware\BAdminCheck;
use think\facade\Session;


class Base extends BaseController
{
    const WEBSITE_CONFIG_PATH = __DIR__.'/../config/' . 'website_config.json';

    protected $middleware = [
        BAdminCheck::class,
    ];

    public function initialize()
    {
        //获取访问地址
        $controller = Request()->controller(); //获取控制器名

        $action = Request()->action();//方法名

        //查询当前用户的权限
        $adminRes = Session::get('bAdmin_admin');

        $authAll = (new Role())->getUserRoleAuth(new B(),$adminRes['role_id']);

        $authAllRes = array_column($authAll,'urls');

        $url = strtolower($controller.'/'.$action);

        if(!in_array($url,$authAllRes)){
//                exit('no_rule');
        }
    }
}
