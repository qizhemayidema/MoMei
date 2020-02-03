<?php
/**
 * Created by : PhpStorm
 * User: fk
 * Date: 19/1/2020
 * Time: 下午3:29
 */

namespace app\common\model;


use app\common\model\impl\BasicImpl;
use think\Model;

class OrderPayStages extends Model implements BasicImpl
{
    public function get($id)
    {
        // TODO: Implement get() method.
        return $this->where('id',$id)->find();
    }

    public function add(array $data): int
    {
        // TODO: Implement add() method.
    }

    public function modify($id, $data)
    {
        // TODO: Implement modify() method.
        return $this->where('id',$id)->update($data);
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