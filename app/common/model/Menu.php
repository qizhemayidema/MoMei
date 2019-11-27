<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use think\Model;

class Menu extends Model implements BasicImpl
{
    public function get($id)
    {
        return $this->find($id);
        // TODO: Implement get() method.
    }

    public function add(Array $data): int
    {
        // TODO: Implement add() method.
    }

    public function modify($id, $data)
    {
        $this->where(['id'=>$id])->update($data);
    }

    public function rm($id)
    {
        // TODO: Implement rm() method.
    }

    public function softDelete($id)
    {

    }

    public function getList($masterType,$level = 1)
    {
        $data = $this->where(['master_type'=>$masterType])->order('order','desc')->select()->toArray();

        $result = $this->getMoreList($data,$level);

        return $result;
    }

    /**
     * 通过pid获取下级
     * @param $masterType
     * @param int $pId
     */
    public function getListForPId($masterType,$pId = 0)
    {

        $data = $this->where(['master_type'=>$masterType,'p_id'=>$pId])->order('order','desc')->select()->toArray();

        return $data;
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
