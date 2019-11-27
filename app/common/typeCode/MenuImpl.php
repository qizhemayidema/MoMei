<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/27
 * Time: 11:35
 */

namespace app\common\typeCode;


interface MenuImpl extends BaseImpl
{
    public function getMasterType() : int;

    public function getLevelType() : int;
}