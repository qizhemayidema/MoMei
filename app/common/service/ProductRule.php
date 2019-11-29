<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/29
 * Time: 17:42
 */

namespace app\common\service;


use app\common\model\Product;

class ProductRule
{
    public function get($id)
    {
         return (new Product())->where(['id'=>$id])->find();

    }

    public function getByCateId($cateId)
    {
        return (new Product())->where(['cate_id'=>$cateId])->find();
    }
}