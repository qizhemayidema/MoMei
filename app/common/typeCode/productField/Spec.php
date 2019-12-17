<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/16
 * Time: 16:00
 */

namespace app\common\typeCode\productField;


class Spec implements \app\common\typeCode\ProductFieldImpl
{
    private $fieldType = 2;
    /**
     * @return mixed
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

}