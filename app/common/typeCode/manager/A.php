<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/3
 * Time: 11:20
 */

namespace app\common\typeCode\manager;


use app\common\typeCode\ManagerImpl;

class A implements ManagerImpl
{
    private $type = 2;

    private $isInfo = true;

    private $field = [
        'address',
        'bus_license',
        'bus_license_code',
        'province',
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


}