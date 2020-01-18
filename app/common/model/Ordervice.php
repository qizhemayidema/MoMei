<?php


namespace app\common\model;


use app\common\model\impl\BasicImpl;
use think\Model;

class Ordervice extends Model implements BasicImpl
{
    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function add(array $data): int
    {
        // TODO: Implement add() method.
        return  $this->insert();
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

    public function addAll($data)
    {
        return $this->insertAll($data);
    }

}