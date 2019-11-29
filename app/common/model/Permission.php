<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\tool\Cache;
use app\common\typeCode\PermissionImpl;
use think\Model;

class Permission extends Model implements BasicImpl
{
    public function getList($type)
    {
        return $this->where(['type'=>$type])->select()->toArray();
    }

    public function get($id) // 获取一条
    {
        return $this->find($id);
    }

    public function add(array $data) : int   //插入一条数据 返回自增主键id
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
     * 查询出这些的权限 将控制器方法组合到一起
     * @param $authArrIds   array  ids集合
     * $data times
     */
    public function userAuthAll($authArrIds)
    {
        $where[] = ['id','in',$authArrIds];
        return $this->field("lower(concat(controller,'/',action)) as urls")->where($where)->select()->toArray();
    }
}
