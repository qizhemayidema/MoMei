<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 13:03
 */

namespace app\common\model;


use app\common\model\impl\BasicImpl;
use think\Model;

class RoleGroup extends Model implements BasicImpl
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

    public function getFindByType($type)
    {
        return $this->where(['type'=>$type])->find();
    }
}