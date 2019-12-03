<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use think\Model;

/**
 * @mixin think\Model
 */
class ManagerInfo extends Model implements BasicImpl
{
    //
    public function get($id)
    {
        // TODO: Implement get() method.
        return $this->where(['id'=>$id])->find();

    }

    public function add(Array $data): int
    {
        // TODO: Implement add() method.
    }

    public function modify($id, $data)
    {
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
}
