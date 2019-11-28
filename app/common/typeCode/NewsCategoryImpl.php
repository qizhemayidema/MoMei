<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 14:41
 */

namespace app\common\typeCode;


interface NewsCategoryImpl extends BaseImpl
{
    public function getLevelType(): int;    //返回层级数量 如果无限则为0
}