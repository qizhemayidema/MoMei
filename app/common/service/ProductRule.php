<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/29
 * Time: 17:42
 */

namespace app\common\service;


use app\common\model\ProductRule as ProductRuleModel;
use app\common\model\ProductField;
use app\common\typeCode\ProductFieldImpl;

class ProductRule
{
    public function get($id)
    {
         return (new ProductRuleModel())->where(['id'=>$id])->find();

    }

    public function getByCateId($cateId)
    {
        return (new ProductRuleModel())->where(['cate_id'=>$cateId])->find();
    }

    public function existsCateId($cateId)
    {
        return ($id = (new ProductRuleModel())->where(['cate_id'=>$cateId])->value('id')) ? $id : 0;
    }

    public function insert($data)
    {
        $insert = [
            'cate_id' => $data['cate_id'],
            'is_open' => $data['is_open'] ?? 1,
            'select_max_sum' => $data['select_max_sum'] ?? 1,
            'sum_unit'      => $data['sum_unit'] ?? '个',
            'cate_name' => $data['cate_name'] ?? '',
            'type' =>  $data['type'] ?? 2,
            'is_screen' => $data['is_screen'] ?? 0,
            'is_level' => $data['is_level'] ?? 1,
            'is_spec'   => $data['is_spec'] ?? 1,
            'is_text'   => $data['is_text'] ?? 1,
            'max_sum' => $data['max_sum'] ?? 1
        ];
        $productModel = (new ProductRuleModel());

        $productModel->insert($insert);

        return $productModel->getLastInsID();
    }

    public function update($data,$id)
    {
        $update = [
            'is_open' => $data['is_open'] ?? 0,
            'select_max_sum' => $data['select_max_sum'],
            'cate_name' => $data['cate_name'],
            'sum_unit'    => $data['sum_unit'],
            'type' => $data['type'],
            'is_screen' => $data['is_screen'],
            'is_level' => $data['is_level'] ?? 1,
            'is_spec'   => $data['is_spec'] ?? 1,
            'is_text'   => $data['is_text'] ?? 1,
            'max_sum' => $data['type'] == 1 ? 40 : 1,
        ];
        $productModel = (new ProductRuleModel());

        $productModel->where(['id'=>$id])->update($update);
    }

    public function getFieldByRuleId(ProductFieldImpl $impl,$ruleId)
    {
        $type = $impl->getFieldType();

        return (new ProductField())->where(['type'=>$type,'product_rule_id'=>$ruleId])->select()->toArray();
    }

    //获取一个规则字段列表
    public function getFieldList(ProductFieldImpl $impl,$cateId)
    {
        $level = new ProductField();

        $type = $impl->getFieldType();

        return $level->where(['type'=>$type,'cate_id'=>$cateId])->select();

    }

    //获取一个字段信息
    public function getField($fieldId)
    {
        $model = new ProductField();

        return is_array($fieldId) ? $model->where(['id'=>$fieldId])->select() : $model->find($fieldId);
    }

    //批量新增
    public function insertField(ProductFieldImpl $impl,$data,$ruleId)
    {
        $insert = [];

        $type = $impl->getFieldType();

        foreach ($data['name'] as $key => $value){
            $insert[] = [
                'type'      => $type,
                'cate_id'    => $data['cate_id'],
                'name' => $value,
                'product_rule_id' => $ruleId,
            ];
        }
        $level = new ProductField();

        $level->insertAll($insert);
    }

    //批量修改 ['id'=>1,'name'=>'123']
    public function updateField($data,$ruleId)
    {
        $level = new ProductField();

        foreach ($data as $key => $value){
             $level->where(['id'=>$value['id'],'product_rule_id'=>$ruleId])->update(['name'=>$value['name']]);
        }
    }

    //获取除ids外的id
    public function getFieldExceptIds(ProductFieldImpl $impl,$ids,$ruleId)
    {
        return (new ProductField())->where(['product_rule_id'=>$ruleId,'type'=>$impl->getFieldType()])
            ->whereNotIn('id',$ids)
            ->column('id');
    }
    //批量删除
    public function deleteField($ids,$ruleId)
    {
        $levelModel = new ProductField();

        if (is_string($ids)){
            $levelModel->where(['id'=>$ids,'product_rule_id'=>$ruleId])->delete();
        }else{
            $levelModel->whereIn('id',$ids)->where(['product_rule_id'=>$ruleId])->delete();
        }
    }

    //删除所有符合impl和ruleId的数据
    public function deleteFieldAll(ProductFieldImpl $impl,$ruleId)
    {
        $levelModel = new ProductField();

        $levelModel->where(['type'=>$impl->getFieldType(),'product_rule_id'=>$ruleId])->delete();
    }
}