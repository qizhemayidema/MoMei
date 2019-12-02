<?php
use app\common\tool\Session;
use app\common\typeCode\role\B;
use app\common\service\Role;
/*判断权限*/
function checkPermission($controller,$action)
{
    //查询当前用户的权限
    $adminRes = (new Session())->getData();

    if($adminRes['role_id']==0){
        return true;
    }

    $authAll = (new Role())->getUserRoleAuth(new B(),$adminRes['role_id']);

    $authAllRes = array_column($authAll,'urls');

    $url = strtolower($controller.'/'.$action);

    if(!in_array($url,$authAllRes)){
        return false;
    }
    return true;
}
