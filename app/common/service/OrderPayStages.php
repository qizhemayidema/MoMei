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
    private $pageLength = false;

    public function setPageLength($value = 15)
    {
        $this->pageLength = $value;
        return $this;
    }

    public function addAll($data)
    {
        return (new OrderPayStagesModel())->insertAll($data);
    }

    public function getList($orderId=false,$applyPayType=false)
    {
        $handler = new OrderPayStagesModel();

        $handler = $applyPayType ?  $handler->whereIn('apply_pay_type',$applyPayType) : $handler;

        $handler = $orderId ?  $handler->where('order_id',$orderId) : $handler;

        return $this->pageLength ?  $handler->paginate(['list_rows'=>$this->pageLength,'query'=>request()->param()]) : $handler->select()->toArray();
    }

    public function get($id)
    {
        return (new OrderPayStagesModel())->get($id);
    }

    public function update($id,$data)
    {
        return (new OrderPayStagesModel())->modify($id,$data);
    }
}