<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/26
 * Time: 12:01
 */

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\tool\Cache;
use app\common\typeCode\role\B;
use think\Model;
class Role extends Model implements BasicImpl
{
    public function get($id) // 获取一条
    {
        return $this->where(['id'=>$id])->find();
    }

    public function getList(\app\common\typeCode\BaseImpl $base,$start=0,$length=10)  // 获取list
    {
        if($base instanceof \app\common\typeCode\Role)
        $type = $base->getRoleType();
        //缓存
        $Cache = new Cache($base);
        if($res = $Cache->getCache()) {
            return $res;
        }else{
            //查询全部的权限组
            $res = $this->where(['type'=>$type])->select()->toArray();
            $Cache->setCache($res);
            return $res;
        }
    }

    public function add(Array $data) : int    //插入一条数据 返回自增主键id
    {
        return $this->insert($data);
    }

    public function modify($id,$data)      //更新一条数据
    {
        return $this->where(['id'=>$id])->update($data);
    }

    public function rm($id)            //删除一条数据
    {
        return $this->where(['id'=>$id])->delete();
    }

    public function softDelete($id)        //软删除
    {

    }

    /**
     * 查询用户所在组的权限
     * @param \app\common\typeCode\Role $role
     * @param $uid
     * $data 2019/11/26 16:32
     */
    public function getUserRoleAuthRes(\app\common\typeCode\Role $role,$roleId){
        $type = $role->getRoleType();
        $where['type'] = $type;
        $where['id'] = $roleId;
        return $this->where($where)->find();
    }
}