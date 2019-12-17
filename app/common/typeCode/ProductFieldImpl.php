<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/16
 * Time: 15:57
 */

namespace app\common\typeCode;


interface ProductFieldImpl extends BaseImpl
{
    public function getFieldType();     //获取自定义字段类型
}