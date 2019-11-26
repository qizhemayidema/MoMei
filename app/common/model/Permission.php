<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\tool\Cache;
use app\common\typeCode\PermissionImpl;
use think\Model;

class Permission extends Model implements BasicImpl
{
    public function getList(\app\common\typeCode\BaseImpl $base, $start = 0, $length = 10)
    {
        // TODO: Implement getList() method.
        if ($base instanceof PermissionImpl){}
        $type = $base->getPermissionType();
        //进行缓存
        $cache = new Cache($base);
        if($res = $cache->getCache()){
            return $res;
        }else{
            //查询对应类型的数据全部的
            $data  = $this->where(['type'=>$type])->select()->toArray();
            $res = $this->getMoreList($data);
            $cache->setCache($res);
            return $res;
        }

    }

    public function get($id) // 获取一条
    {

    }

    public function add(array $data) : int   //插入一条数据 返回自增主键id
    {
        return 1;
    }

    public function modify($id,$data)      //更新一条数据
    {

    }

    public function rm($id)            //删除一条数据
    {

    }

    public function softDelete($id)        //软删除
    {

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
