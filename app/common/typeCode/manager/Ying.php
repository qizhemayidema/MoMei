<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/3
 * Time: 15:06
 */

namespace app\common\typeCode\manager;


use app\common\typeCode\ManagerImpl;

class Ying implements ManagerImpl
{
    private $type = 3;

    private $isInfo = true;

    private $field = [
        'address',
        'bus_license',
        'province',
        'work_type',
        'city',
        'county',
        'contact',
        'tel',
        'contact_license_code',
        'contact_license_pic',
        'contact_sex',
        'contact_tel',
        'contact_wechat',
        'credit_code',
        'email',
        'name',
        'pro_id',
        'type',
        'pro_name',
        'add_time',];

    public function getManagerType()
    {
        // TODO: Implement getManagerType() method.
        return $this->type;

    }

    public function isInfo()
    {
        // TODO: Implement isInfo() method.
        return $this->isInfo;
    }

    public function getInfoField()
    {
        // TODO: Implement getInfoField() method.
        return $this->field;
    }

    public function setIsInfo($value=false){
        $this->isInfo = $value;

        return $this;
    }

}