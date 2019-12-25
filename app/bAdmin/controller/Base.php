<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\BaseController;
use app\common\service\Role;
use app\common\tool\Session;
use app\common\typeCode\role\B;
use app\middleware\BAdminCheck;
use think\facade\View;


class Base extends BaseController
{
    const WEBSITE_CONFIG_PATH = __DIR__.'/../config/' . 'website_config.json';

    protected $userAuth = null;

    protected $middleware = [
        BAdminCheck::class,
    ];

    public function initialize()
    {
        $this->setMenu();

        View::assign('base',$this);

        if (!$this->checkPermissionWithController()) {
            if (request()->isAjax() || request()->isPost()) {
                header('Content-type: application/json');
                exit(json_encode(['code' => 0, 'msg' => '操作越权'], 256));

            } else {
                return redirect("/Index/index");
            }
        }
    }


    protected function setMenu()
    {
        $menu = (new \app\common\service\Menu((new \app\common\typeCode\menu\B())))->getList();

        View::assign('menu',$menu);
    }

    public function checkPermissionWithController($controller = '')
    {

        $controller = $controller ? $controller : Request()->controller(); //获取控制器名

        $permission = strtolower($controller);

        $except = [
            'index',
        ];

        if (in_array($permission,$except)) return true;

        //查询当前用户的权限
        $adminRes = (new Session())->getData();

        if($adminRes['role_id']==0)  return true;

        if (!$this->userAuth){
            $authAll = array_column((new Role())->getUserRoleAuthForIds(new B(),$adminRes['role_id'])->toArray(),'controller');

            $temp = [];

            foreach ($authAll as $k => $v) {
                $temp[] = strtolower($v);
            }

            $this->userAuth = $temp;
        }

        if(!in_array($permission,$this->userAuth)) return false;

        return true;

//        $controller = $controller ? $controller : Request()->controller(); //获取控制器名
//
//        $permission = strtolower($controller . '/#');
//
//        $except = [
//            'index/#',
//        ];
//
//        if (in_array($permission,$except)) return true;
//
//        //查询当前用户的权限
//        $adminRes = (new Session())->getData();
//
//        if($adminRes['role_id']==0)  return true;
//
//        if (!$this->userAuth){
//            $authAll = (new Role())->getUserRoleAuth(new B(),$adminRes['role_id']);
//            $this->userAuth = array_column($authAll,'urls');
//        }
//
//
//        if(!in_array($permission,$this->userAuth)) return false;
//
//        return true;
    }

    protected function checkPermission()
    {

        if(request()->isGet()) return true;

        $controller = Request()->controller(); //获取控制器名

        $action = Request()->action();//方法名

        //查询当前用户的权限
        $adminRes = (new Session())->getData();

        if($adminRes['role_id']==0)  return true;

        $authAll = (new Role())->getUserRoleAuth(new B(),$adminRes['role_id']);

        $authAllRes = array_column($authAll,'urls');

        $url = strtolower($controller.'/'.$action);

        $ignorePermission = ['index/index','auser/uploadpic','productrule/edit','productrule/getcatelist','newsproduct/uploadpic','cinema/getarea','cinema/uploadpic','areahot/getareabypid'];

        if (in_array($url,$ignorePermission)) return true;

        if(!in_array($url,$authAllRes)) return false;

        return true;
    }
}
