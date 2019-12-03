<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/3
 * Time: 11:19
 */

namespace app\common\typeCode\manager;


use app\common\typeCode\ManagerImpl;

class B implements ManagerImpl
{
    private $type = 1;

    private $isInfo = false;

    private $field = [];

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