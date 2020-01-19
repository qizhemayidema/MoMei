<?php


namespace app\common\service;

use app\common\model\Order as OrderModel;
use app\common\model\Ordervice as OrderviceModel;
class Order
{
    private $where = [];

    private $groupCode = false;

    private $userId = false;

    private $pageLength = null;

    public function setWhere($field,$symbol,$value)
    {
        $this->where[0] = $field;
        $this->where[1] = $symbol;
        $this->where[2] = $value;
        return $this;
    }

    public function setGroupCode($value)
    {
        $this->groupCode = $value;
        return $this;
    }

    public function setUserId($value)
    {
        $this->userId = $value;
        return $this;
    }

    public function setPageLength($value){
        $this->pageLength = $value;
        return $this;
    }

    public function getOrderCount($uid)
    {
        $handler = new OrderModel();

        $handler = $handler->where('user_id',$uid);

        $handler = count($this->where) ? $handler->where($this->where[0],$this->where[1],$this->where[2])->count() : $handler;

        return $handler;
    }

    public function addDataId($data)
    {
        return (new OrderModel())->addId($data);
    }

    public function getList()
    {
        $handler = new OrderModel();

        $handler = $this->userId ? $handler->where('user_id',$this->userId) : $handler;

        $handler = $this->groupCode ? $handler->where('cinema_id',$this->groupCode) : $handler;

        return $this->pageLength ? $handler->paginate(['list_rows'=>$this->pageLength,'query'=>request()->param()]) : $handler->select()->toArray();
    }

    public function getOneData($id)
    {
        $handler = new OrderModel();

        $handler = $handler->alias('order');

        $handler = $this->groupCode ? $handler->where('cinema_id',$this->groupCode) : $handler;

        $handler = $handler->join('ordervice','order.id=ordervice.o_id')->where('order.id',$id)->field('order.*,ordervice.*,ordervice.id as v_id');

        return $handler->select()->toArray();
    }

    public function get($id)
    {
        return (new OrderModel())->get($id);
    }

    public function updateData($data,$selfId=false)
    {
        $id = $data['id'];
        $updata = [
            'agreement_code' =>$data['agreement_code'],
            'agreement' =>$data['agreement'],
            'pay_type' =>$data['pay_type'],
            'money_type' =>$data['money_type'],
            'other_price' =>$data['other_price'],
            'all_price' =>$data['all_price'],
            'price' =>$data['price'],
        ];
        $handler = new OrderModel();

        $handler = $selfId ? $handler->where('cinema_id',$selfId) : $handler;

        return $handler->where('id',$id)->update($updata);
    }
}