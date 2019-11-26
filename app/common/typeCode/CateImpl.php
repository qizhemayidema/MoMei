<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 9:57
 */

namespace app\common\typeCode;


interface CateImpl extends BaseImpl
{
    public function getCateType() :int;     //获取分类类型

    public function getLevelType(): int;    //返回层级数量 如果无限则为0

    public function setMasterId($id);       //设置 所属用户 id

    public function getMasterId() : int;    //获取 所属影院用户id

}