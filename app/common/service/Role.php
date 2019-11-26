<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/26
 * Time: 11:59
 */

namespace app\common\service;

use app\common\tool\Cache;
use app\common\typeCode\BaseImpl;
use app\common\typeCode\CacheImpl;

class Role
{
    /**
     * 查询全部的权限组(角色)
     * $data 2019/11/26 12:00
     */
    public function getRoleList(BaseImpl $obj)
    {
        return (new \app\common\model\Role())->getList($obj);
    }

    public function insert(\app\common\typeCode\Role $Role,$data)
    {
        $type = $Role->getRoleType();
        $dataRes = [
            'type'=>$type,
            'role_name'=>$data['role_name'],
            'role_desc'=>$data['role_desc'],
            'permission_ids'=>implode(',',$data['rules']).',',
        ];

        if($Role instanceof CacheImpl){
            (new Cache($Role))->clear();
        }

        return (new \app\common\model\Role())->add($dataRes);
    }

    /**
     * 查找一条记录
     * @param $id
     * @return array
     * $data 2019/11/26 10:29
     */
    public function getFindRes($id)
    {
        return (new \app\common\model\Role())->get($id);
    }


    public function updataRes(\app\common\typeCode\Role $Role,$data)
    {
        $type = $Role->getRoleType();
        $updata = [
            'type'=>$type,
            'role_name'=>$data['role_name'],
            'role_desc'=>$data['role_desc'],
            'permission_ids'=>implode(',',$data['rules']).',',
        ];
        //更新缓存
        if($Role instanceof  CacheImpl){
            (new Cache($Role))->clear();
        }
        return (new \app\common\model\Role())->modify($data['id'],$updata);
    }

    public function delete(CacheImpl $CacheImpl,$id)
    {
        if($CacheImpl instanceof CacheImpl){
            (new Cache($CacheImpl))->clear();
        }
        return (new \app\common\model\Role())->rm($id);
    }

    /**
     * 查询用户权限
     * @param \app\common\typeCode\Role $Role
     * @param $uid
     * $data 2019/11/26 16:30
     */
    public function getUserRoleAuth(\app\common\typeCode\Role $Role,$roleId)
    {
        //查询用户所在的权限组
        $getUserRoleAuthRes = (new \app\common\model\Role())->getUserRoleAuthRes($Role,$roleId);
        //查询该权限组所拥有的具体权限
        $authArrIds = explode(',',rtrim($getUserRoleAuthRes['permission_ids'],','));
        $userAuthAllRes = (new \app\common\model\Permission())->userAuthAll($authArrIds);
        return $userAuthAllRes;
    }
}