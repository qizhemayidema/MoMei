<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 12:57
 */

namespace app\common\typeCode;


interface RoleGroupImpl extends BaseImpl
{
    public function getRoleGroupType() : int ;  //获取类型
}