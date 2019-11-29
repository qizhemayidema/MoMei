<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\tool\Cache;
use app\common\typeCode\CacheImpl;
use app\common\typeCode\CateImpl;
use think\Model;

class Category extends Model implements BasicImpl
{
    public function get($id)
    {
        return $this->find($id);
    }

    /**
     * @param $type int 类型
     * @param int $level 层级
     * @param int $masterId 所属用户id
     * @return array
     */
    public function getList($type,$level = 1,$masterId = 0)
    {
        $data = $this->where(['type'=>$type,'master_id' => $masterId])->order('order_num','desc')
            ->select()->toArray();

        $return = $this->getMoreList($data,$level);

        return $return;
    }

    public function add(array $data): int
    {
        $this->insert($data);

        return (int)$this->getLastInsID();
    }

    public function modify($id, $data)
    {
        $this->where(['id'=>$id])->update($data);
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
