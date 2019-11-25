<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\typeCode\CacheImpl;
use app\common\typeCode\CateImpl;
use think\Model;

class Category extends Model implements BasicImpl
{
    public function get(\app\common\typeCode\Base $base)
    {

    }

    public function getList(\app\common\typeCode\Base $base, $start = 0, $length = 10)
    {
        if ($base instanceof CateImpl){

        }

        $type = $base->getCateType();

        $level = $base->getLevelType();

        $masterId = $base->getMasterId();

        if ($base instanceof CacheImpl){
            if ($res =$base->getCache()){
                return $res;
            }
        }
        $data = $this->where(['type'=>$type,'master_id' => $masterId])->order('order_num','desc')
            ->select()->toArray();

        $result = $this->getMoreList($data,$level);

        if ($base instanceof CacheImpl){
            $base->setCache($result);
        }

        return $result;
    }

    public function add(array $data): int
    {
    }

    public function modify($id, $data)
    {
    }

    public function softDelete($id)
    {
    }

    public function rm($id)
    {
    }

    private function getMoreList($categorys,$max = 1,$pId = 0,$l = 0)
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
