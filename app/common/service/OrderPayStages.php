<?php
/**
 * Created by : PhpStorm
 * User: fk
 * Date: 19/1/2020
 * Time: 下午3:29
 */

namespace app\common\service;

use app\common\model\OrderPayStages as OrderPayStagesModel;
class OrderPayStages
{
    public function addAll($data)
    {
        return (new OrderPayStagesModel())->insertAll($data);
    }

    public function getList($orderId)
    {
        return (new OrderPayStagesModel())->where('order_id',$orderId)->select()->toArray();
    }
}