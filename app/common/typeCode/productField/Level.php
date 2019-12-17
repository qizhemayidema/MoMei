<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/16
 * Time: 15:58
 */

namespace app\common\typeCode\ProductField;

class Level implements \app\common\typeCode\ProductFieldImpl
{
    private $fieldType = 1;
    /**
     * @return mixed
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

}