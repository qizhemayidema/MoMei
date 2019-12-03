<?php
declare (strict_types = 1);

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use think\Model;

/**
 * @mixin think\Model
 */
class CategoryObjHaveAttr extends Model implements BasicImpl
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param array $data
     * @return int
     */
    public function add(Array $data): int
    {
        // TODO: Implement add() method.
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function modify($id, $data)
    {
        // TODO: Implement modify() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function rm($id)
    {
        // TODO: Implement rm() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    public function softDelete($id)
    {
        // TODO: Implement softDelete() method.
    }

}
