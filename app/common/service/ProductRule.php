<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/29
 * Time: 17:42
 */

namespace app\common\service;


use app\common\model\Product;
use app\common\model\ProductLevel;

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

    public function existsCateId($cateId)
    {
        return ($id = (new Product())->where(['cate_id'=>$cateId])->value('id')) ? $id : 0;
    }

    public function insert($data)
    {
        $insert = [
            'cate_id' => $data['cate_id'],
            'select_max_sum' => $data['select_max_sum'],
            'cate_name' => $data['cate_name'],
            'type' =>  $data['type'],
            'is_screen' => $data['is_screen'],
            'is_level' => $data['is_level'],
//            'sum'       => $data['sum'],
        ];
        $productModel = (new Product());

        $productModel->insert($insert);

        return $productModel->getLastInsID();
    }

    public function update($data,$id)
    {
        $update = [
            'select_max_sum' => $data['select_max_sum'],
            'cate_name' => $data['cate_name'],
            'type' => $data['type'],
            'is_screen' => $data['is_screen'],
            'is_level' => $data['is_level'],
        ];
        $productModel = (new Product());

        $productModel->where(['id'=>$id])->update($update);
    }

    public function getLevelByProductId($productId)
    {
        return (new ProductLevel())->where(['product_id'=>$productId])->select()->toArray();
    }

    //获取一个规则的级别
    public function getLevelList($cateId)
    {
        $level = new ProductLevel();
        return $level->where(['cate_id'=>$cateId])->select();

    }

    //获取一个级别信息
    public function getLevel($levelId)
    {
        return (new ProductLevel())->find($levelId);
    }

    //批量新增
    public function insertLevel($data,$productId)
    {
        $insert = [];

        foreach ($data['level_name'] as $key => $value){
            $insert[] = [
                'cate_id'    => $data['cate_id'],
                'level_name' => $value,
                'product_id' => $productId,
            ];
        }
        $level = new ProductLevel();

        $level->insertAll($insert);
    }
    //批量修改 ['id'=>1,'level_name'=>'123']

    public function updateLevel($data,$productId)
    {
        $level = new ProductLevel();
        foreach ($data as $key => $value){
             $level->where(['id'=>$value['id'],'product_id'=>$productId])->update(['level_name'=>$value['level_name']]);
        }
    }
    //获取除ids外的id

    public function getLevelExceptIds($ids,$productId)
    {
        return (new ProductLevel())->where(['product_id'=>$productId])
            ->whereNotIn('id',$ids)
            ->column('id');
    }
    //批量删除

    public function deleteLevel($ids,$productId)
    {
        $levelModel = new ProductLevel();
        if (is_string($ids)){
            $levelModel->where(['id'=>$ids,'product_id'=>$productId])->delete();
        }else{
            $levelModel->whereIn('id',$ids)->where(['product_id'=>$productId])->delete();
        }
    }
}