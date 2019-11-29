<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:14
 */
namespace app\common\service;

use app\common\tool\Cache;
use app\common\typeCode\BaseImpl;
use app\common\typeCode\CacheImpl;
use app\common\typeCode\PermissionImpl;

class Permission
{
    /**
     * 查询全部的权限
     * $data 2019/11/25 17:36
     */
    public function getRuleList(PermissionImpl $obj)
    {

        $type = $obj->getPermissionType();
        if($obj instanceof CacheImpl){
            $cache = new Cache($obj);
            if($res = $cache->getCache()){
                return $res;
            }else{
                //查询对应类型的数据全部的
                $data  = (new \app\common\model\Permission())->getList($type);
                $res = $this->getMoreList($data);
                $cache->setCache($res);
                return $res;
            }
        }else{
            //查询对应类型的数据全部的
            $data  = (new \app\common\model\Permission())->getList($type);
            $res = $this->getMoreList($data);
            return $res;
        }

    }

    /**
     * 添加权限
     * $data 2019/11/26 9:33
     */
    public function insert(PermissionImpl $PermissionImpl,$data)
    {
        $rdata = Array(
            'name'=>$data['name'],
            'controller'=>$data['controller'],
            'action'=>$data['action'],
            'p_id'=>$data['p_id'],
            'type'=>$PermissionImpl->getPermissionType(),
        );

        if($PermissionImpl instanceof CacheImpl){
            (new Cache($PermissionImpl))->clear();
        }

        return (new \app\common\model\Permission())->add($rdata);
    }

    /**
     * 查找一条记录
     * @param $id
     * @return array
     * $data 2019/11/26 10:29
     */
    public function getFindRes($id)
    {
        return (new \app\common\model\Permission())->get($id);
    }

    /**
     * 修改
     * @param CacheImpl $CacheImpl
     * @param $data
     * @return \app\common\model\Permission
     * $data times
     */
    public function updataRes(CacheImpl $CacheImpl,$data)
    {
        $updata = [
            'name'=>$data['name'],
            'controller'=>$data['controller'],
            'action'=>$data['action'],
            'p_id'=>$data['p_id'],
        ];
        //更新缓存
        if($CacheImpl instanceof  CacheImpl){
            (new Cache($CacheImpl))->clear();
        }
        return (new \app\common\model\Permission())->modify($data['id'],$updata);
    }

    public function delete(CacheImpl $CacheImpl,$id)
    {
        $permissionAll = (new \app\common\model\Permission())->getPermissionByPId($id);

        if(!empty($permissionAll)){
            $ids = array_column($permissionAll,'id');
            $ids[] = $id;
            $result = (new \app\common\model\Permission())->deleteIds($ids);
        }else{
            $result = (new \app\common\model\Permission())->rm($id);
        }

        if(!$result) return false;


        if($CacheImpl instanceof CacheImpl){
            (new Cache($CacheImpl))->clear();
        }

        return true;
    }

    private function getMoreList($categorys,$max = 2,$pId = 0,$l = 0)
    {
        $list = [];
        foreach ($categorys as $k=>$v){
            if ($v['p_id'] == $pId){
                unset($categorys[$k]);
                if ($l < $max){
                    //小于三级
                    $v['children'] = $this->getMoreList($categorys,$max,$v['id'],$l+1);
                }
                $list[] = $v;
            }
        }
        return $list;
    }
}