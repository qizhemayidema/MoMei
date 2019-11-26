<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\middleware\BAdminCheck;
use think\facade\Session;


class Base extends BaseController
{
    const WEBSITE_CONFIG_PATH = __DIR__.'/../config/' . 'website_config.json';

    protected $middleware = [
        BAdminCheck::class,
    ];

    public function __construct()
    {
        //获取访问地址
        $controller = Request()->controller(); //获取控制器名
        $action = Request()->action();//方法名
        //查询当前用户的权限
        $adminRes = Session::get('bAdmin_admin');
    }
}
