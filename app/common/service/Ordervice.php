<?php
namespace app\common\service;


use app\common\model\Ordervice as OrderviceModel;

class Ordervice
{
    public function addAllData($data)
    {
        return (new OrderviceModel())->addAll($data);
    }
}