<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/25
 * Time: 18:04
 */

namespace app\common\typeCode;


interface PermissionImpl extends  BaseImpl
{
    public function getPermissionType():int;  //获取类型分类
}