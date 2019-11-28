<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 12:56
 */

namespace app\common\service;


use app\common\typeCode\RoleGroupImpl;

class RoleGroup
{
    public function getFindRoleGroup(RoleGroupImpl $RoleGroupImpl)
    {
        return (new \app\common\model\RoleGroup())->getFindByType($RoleGroupImpl->getRoleGroupType());
    }
}