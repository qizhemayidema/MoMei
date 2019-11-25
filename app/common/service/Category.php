<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 16:07
 */

namespace app\common\service;


use app\common\typeCode\cate\ABusCate;

class Category
{
    public function getABusList()
    {
        return (new \app\common\model\Category())->getList(new ABusCate());
    }
}