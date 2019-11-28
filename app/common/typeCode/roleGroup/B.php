<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 13:21
 */

namespace app\common\typeCode\roleGroup;


use app\common\typeCode\RoleGroupImpl;

class B implements RoleGroupImpl
{
    private $type = false;

    public function getRoleGroupType(): int
    {
        // TODO: Implement getRoleGroupType() method.
        return $this->type;
    }

}