<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/26
 * Time: 11:59
 */

namespace app\common\service;

use app\common\tool\Cache;
use app\common\typeCode\CacheImpl;
use app\common\typeCode\RoleGroupImpl;

class Role
{
    /**
     * 查询全部的权限组(角色)
     * $data 2019/11/26 12:00
     */
    public function getRoleList(\app\common\typeCode\RoleImpl $obj)
    {
        $type = $obj->getRoleType();
        //缓存
        if($obj instanceof CacheImpl){
            $cache = new Cache($obj);
            if($res = $cache->getCache()) {
                return $res;
            }else{
                //查询全部的权限组
                $res = (new \app\common\model\Role())->getList($type);
                $cache->setCache($res);
                return $res;
            }
        }else{
            //查询全部的权限组
            $res = (new \app\common\model\Role())->getList($type);
            return $res;
        }

    }

    public function insert(\app\common\typeCode\RoleImpl $Role,$groupCode,$data)
    {
        $type = $Role->getRoleType();
        $dataRes = [
            'type'=>$type,
            'role_name'=>$data['role_name'],
            'role_desc'=>$data['role_desc'],
            'permission_ids'=>implode(',',$data['rules']).',',
        ];

        if($groupCode) $dataRes['group_code'] = $groupCode;

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


    public function updateRes(\app\common\typeCode\RoleImpl $Role,$data)
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
     * @param \app\common\typeCode\RoleImpl $Role
     * @param $uid
     * $data 2019/11/26 16:30
     */
    public function getUserRoleAuth(\app\common\typeCode\RoleImpl $Role,$roleId)
    {
        //查询用户所在的权限组
        $getUserRoleAuthRes = (new \app\common\model\Role())->getUserRoleAuthRes($Role,$roleId);
        //查询该权限组所拥有的具体权限
        $authArrIds = explode(',',rtrim($getUserRoleAuthRes['permission_ids'],','));
        $userAuthAllRes = (new \app\common\model\Permission())->userAuthAll($authArrIds);
        return $userAuthAllRes;
    }
}