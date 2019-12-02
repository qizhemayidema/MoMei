<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use app\common\model\impl\ShowImpl;
use think\Model;


class Cinema extends Model implements BasicImpl,ShowImpl
{
    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function add(Array $data): int
    {
        // TODO: Implement add() method.
    }

    public function modify($id, $data)
    {
        $this->where(['id'=>$id])->update($data);
        // TODO: Implement modify() method.
    }

    public function rm($id)
    {
        // TODO: Implement rm() method.
    }

    public function softDelete($id)
    {
        // TODO: Implement softDelete() method.
    }

    public function receptionShowData(string $alias = '')
    {
        $alias = $alias ? $alias . '.' : '';

        $this->where([$alias.'status'=>1]);
    }

    public function backgroundShowData(string $alias = '')
    {
        return $this;
    }

}
