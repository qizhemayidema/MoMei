<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 10:52
 */

namespace app\common\typeCode;


interface GroupImpl extends BaseImpl
{
    public function getGroupType() : int;  //类型
}