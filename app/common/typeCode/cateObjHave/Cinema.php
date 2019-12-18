<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/18
 * Time: 13:30
 */


namespace app\common\typeCode\cateObjHave;

use app\common\typeCode\CateObjHaveImpl;

class Cinema implements CateObjHaveImpl
{
    private $objType = 1;

    /**
     * @return int
     */
    public function getObjType()
    {
        return $this->objType;
    }

}