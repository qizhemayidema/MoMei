<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/4
 * Time: 13:25
 */

namespace app\common\typeCode;


interface BoxOfficeImpl extends BaseImpl
{
    public function getBoxType() : int ;  //获取类型
}