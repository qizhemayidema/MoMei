<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\common\service\Role;
use app\common\typeCode\role\B;
use app\middleware\BAdminCheck;
use think\facade\Session;
use think\facade\View;


class Base extends BaseController
{
    const WEBSITE_CONFIG_PATH = __DIR__.'/../config/' . 'website_config.json';

    protected $middleware = [
        BAdminCheck::class,
    ];

    public function initialize()
    {
        $this->setMenu();

        $this->checkPermission();
    }


    protected function setMenu()
    {
        $menu = (new \app\common\service\Menu((new \app\common\typeCode\menu\B())))->getList();

        View::assign('menu',$menu);
    }

    protected function checkPermission()
    {
        //获取访问地址
        $controller = Request()->controller(); //获取控制器名

        $action = Request()->action();//方法名

        //查询当前用户的权限
        $adminRes = Session::get('bAdmin_admin');

        if($adminRes['role_id']==0)  return true;

        $authAll = (new Role())->getUserRoleAuth(new B(),$adminRes['role_id']);

        $authAllRes = array_column($authAll,'urls');

        $url = strtolower($controller.'/'.$action);

        $ignorePermission = ['index/index'];

        if(!in_array($url,$ignorePermission) && !in_array($url,$authAllRes)){
//            return redirect('/index/index');
        }
    }
}
