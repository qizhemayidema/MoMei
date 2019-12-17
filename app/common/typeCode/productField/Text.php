<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/16
 * Time: 16:01
 */

namespace app\common\typeCode\ProductField;


use app\common\typeCode\ProductFieldImpl;

class Text implements ProductFieldImpl
{

    private $fieldType = 3;
    /**
     * @return mixed
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

}