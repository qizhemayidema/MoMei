<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\BaseController;
use app\common\service\Role;
use app\common\tool\Session;
use app\common\typeCode\role\Cinema;
use app\middleware\CinemaAdminCheck;
use think\facade\View;
use app\common\service\Menu as MenuService;
use think\Request;

class Base extends BaseController
{
    const WEBSITE_CONFIG_PATH = __DIR__.'/../config/' . 'website_config.json';

    protected $middleware = [
        CinemaAdminCheck::class,
    ];

    public function initialize()
    {
        if (!$this->checkPermission()) {
            if (request()->isAjax() || request()->isPost()) {
                header('Content-type: application/json');
                exit(json_encode(['code' => 0, 'msg' => '操作越权'], 256));

            } else {
                return redirect("/Index/index");
            }
        }

        $this->setMenu();
    }


    protected function setMenu()
    {
        $menu = (new MenuService((new \app\common\typeCode\menu\Cinema())))->getList();

        View::assign('menu',$menu);
    }

    protected function checkPermission()
    {
        if(request()->isGet()) return true;

        $controller = Request()->controller(); //获取控制器名

        $action = Request()->action();//方法名

        //查询当前用户的权限
        $adminRes = (new Session())->getData();

        if($adminRes['role_id']==0)  return true;

        $authAll = (new Role())->getUserRoleAuth(new Cinema(),$adminRes['role_id']);

        $authAllRes = array_column($authAll,'urls');

        $url = strtolower($controller.'/'.$action);

        $ignorePermission = ['index/index'];

        if (in_array($url,$ignorePermission)) return true;

        if(!in_array($url,$authAllRes)) return false;
        return true;
    }
}

