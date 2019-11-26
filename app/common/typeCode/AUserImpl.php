<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 15:40
 */

namespace app\common\typeCode;



interface AUserImpl extends BaseImpl
{
    public function getUserType() : int ;
}