<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 12:59
 */

namespace app\common\typeCode\roleGroup;


use app\common\typeCode\RoleGroupImpl;

class C implements RoleGroupImpl
{
    private $type = 1;
    public function getRoleGroupType(): int
    {
        // TODO: Implement getRoleGroupType() method.
        return $this->type;
    }

}