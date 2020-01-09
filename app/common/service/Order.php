<?php


namespace app\common\service;

use app\common\model\Order as OrderModel;
class Order
{
    private $where = [];

    public function setWhere($field,$symbol,$value)
    {
        $this->where[0] = $field;
        $this->where[1] = $symbol;
        $this->where[2] = $value;
        return $this;
    }

    public function getOrderCount($uid)
    {
        $handler = new OrderModel();

        $handler = $handler->where('user_id',$uid);

        $handler = count($this->where) ? $handler->where($this->where[0],$this->where[1],$this->where[2])->count() : $handler;

        return $handler;
    }
}