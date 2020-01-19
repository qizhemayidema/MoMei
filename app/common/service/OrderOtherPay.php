<?php
/**
 * Created by : PhpStorm
 * User: fk
 * Date: 19/1/2020
 * Time: 下午3:12
 */

namespace app\common\service;

use app\common\model\OrderOtherPay as OrderOtherPayModel;
class OrderOtherPay
{
    public function addAll($data)
    {
        return (new OrderOtherPayModel())->addAll($data);
    }

    public function getList($orderId)
    {
        return (new OrderOtherPayModel())->where('order_id',$orderId)->select()->toArray();
    }
}