<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 13:00
 */

namespace app\common\typeCode\roleGroup;


use app\common\typeCode\RoleGroupImpl;

class M implements RoleGroupImpl
{
    private $type = 2;
    public function getRoleGroupType() : int  {
        // TODO: Implement getRoleGroupType() method.
        return $this->type;
    }
}