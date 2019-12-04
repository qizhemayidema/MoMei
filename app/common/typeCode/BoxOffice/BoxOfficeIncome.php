<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/4
 * Time: 13:27
 */

namespace app\common\typeCode\BoxOffice;


use app\common\typeCode\BoxOfficeImpl;

class BoxOfficeIncome implements BoxOfficeImpl
{
    private $type = 1;

    public function getBoxType():int  //获取类型
    {
        return $this->type;
    }
}