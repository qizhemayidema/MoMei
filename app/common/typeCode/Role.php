<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/26
 * Time: 12:45
 */

namespace app\common\typeCode;


interface Role extends BaseImpl
{
    public function getRoleType():int ; //获取类型
}