<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/3
 * Time: 11:21
 */

namespace app\common\typeCode\manager;


use app\common\typeCode\ManagerImpl;

class Cinema implements ManagerImpl
{
    private $type = 3;

    private $isInfo = true;

    private $field = [
        'yuan_id',
        'tou_id' ,
        'area_id',
        'area_value',
        'bus_area',
        'property_company',
        'address',
        'bus_license',
        'bus_license_code',
        'province_id' ,
        'province',
        'city_id',
        'city' ,
        'county_id',
        'county' ,
        'contact' ,
        'tel' ,
        'contact_license_code' ,
        'contact_license_pic',
        'contact_sex' ,
        'contact_tel' ,
        'contact_wechat',
        'credit_code',
        'email',
        'name' ,
        'pro_id' ,
        'pro_name' ,
        'duty' ,
        'duty_tel' ,
        'create_time',
        ];
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