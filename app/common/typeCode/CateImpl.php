<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 9:57
 */

namespace app\common\typeCode;


interface CateImpl extends Base
{
    public function getCateType() :int;     //获取分类类型

    public function getLevelType(): int;     //返回层级数量 如果无限则为0

}