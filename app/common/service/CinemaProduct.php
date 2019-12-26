<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/4
 * Time: 16:11
 */

namespace app\common\service;


use app\common\model\CinemaProduct as CinemaProductModel;
use app\common\model\CinemaProductField;
use app\common\model\CinemaProductStatus;
use app\common\model\ProductRule;

use app\common\model\ProductField;
use app\common\typeCode\ProductFieldImpl;

class CinemaProduct
{
    private $groupCode;

    private $showType = true;     //true 面向后台数据  false 面向用户数据


    public function __construct($groupCode = null)
    {
        $this->groupCode = $groupCode;
    }

    public function setShowType($bool = true)
    {
        $this->showType = $bool;

        return $this;
    }

    public function get($id)
    {
        return (new CinemaProductModel())->where(['cinema_id'=>$this->groupCode])->find($id);
    }

    public function insert($data)
    {
        $insert = [
            'cinema_id'         => $this->groupCode,
            'cate_id'           => $data['cate_id'    ],
            'screen_id'         => $data['screen_id'  ],
            'cate_name'         => $data['cate_name'  ],
            'cinema_name'       => $data['cinema_name'],
            'screen_name'       => $data['screen_name'],
            'entity_name'       => $data['entity_name'],
            'pic'               => $data['pic'],
            'roll_pic'          => $data['roll_pic'],
            'desc'              => $data['desc'],
//            'sort'              => $data['sort'       ],
            'price_json'        => $data['price_json' ],
            'price_month'       => $data['price_month'],
            'price_year'        => $data['price_year' ],
            'price_discount_month'    => $data['price_discount_month'] ?? 0,
            'price_discount_year'    => $data['price_discount_year'] ?? 0,
            'status'            => 2,
            'create_time'       => time(),
        ];

        $model = (new CinemaProductModel());
        $model->insert($insert);

        return $model->getLastInsID();

    }

    public function update($id,$data)
    {
        $update = [
            'screen_id'         => $data['screen_id'],
            'cinema_name'       => $data['cinema_name'],
            'entity_name'       => $data['entity_name'],
            'desc'              => $data['desc'],
            'screen_name'       => $data['screen_name'],
            'cate_name'         => $data['cate_name'],
            'price_json'        => $data['price_json' ],
            'price_month'       => $data['price_month'],
            'price_year'        => $data['price_year' ],
            'pic'               => $data['pic'],
            'roll_pic'          => $data['roll_pic'],
            'price_discount_month'    => $data['price_discount_month'] ?? 0,
            'price_discount_year'    => $data['price_discount_year'] ?? 0,
            'status'    => 2,
        ];

        \app\common\model\CinemaProduct::where(['id'=>$id])->update($update);

    }

    public function delete($entityId)
    {
        CinemaProductModel::where(['id'=>$entityId,'cinema_id'=>$this->groupCode])->update(['delete_time'=>time()]);
    }

    public function changeStatus($id,$status)
    {

        (new CinemaProductModel())->where(['cinema_id'=>$this->groupCode,'id'=>$id])->update(['status'=>$status]);
    }


    //检查某个组合的数量
    public function getSum($cate_id,$level_id = 0,$screen_id = 0,$exceptId = [])
    {
        $where = [
            'cinema_id' => $this->groupCode,
            'cate_id' => $cate_id,
        ];

//        $level_id && $where['level_id'] = $level_id;
        $screen_id && $where['screen_id'] = $screen_id;

        return (new CinemaProductModel())->backgroundShowData()->where($where)->whereNotIn('id',$exceptId)->count();

    }


/*-----------------------------------------------------*/


    public function getEntity($entityId)
    {
        return (new \app\common\model\CinemaProduct())->where(['cinema_id'=>$this->groupCode])->find($entityId);
    }

    public function getEntityList($page = null,$cateId = null,$screenId = null)
    {
        $handler = (new CinemaProductModel());

        $where = [];

        $cateId && $where['cate_id'] = $cateId;

        $screenId && $where['screen_id'] = $screenId;

        $this->groupCode && $where['cinema_id'] = $this->groupCode;

        $handler = $this->showType ? $handler->backgroundShowData() : $handler->receptionShowData();

        $where && $handler = $handler->where($where);

        $handler = $handler->order('sort','desc');

        return $page ? $handler->paginate(['list_rows'=>$page,'query'=>request()->param()]) : $handler->select();
    }


/*------------------------------------------------------------------------------*/

    //如果不存在此日期则插入
    public function insertEntityDayPriceStatus($entityId,$dayJsonStr)
    {
        $insert = [];

        $date = (new CinemaProductStatus())->where(['entity_id'=>$entityId])->column('date');

        $arr = json_decode($dayJsonStr,true);

        foreach ($arr as $key => $value){
            if (!in_array(strtotime($value['date']),$date)){
                $insert[] = [
                    'entity_id' => $entityId,
                    'date'      => strtotime($value['date']),
                    'status'    => 0,
                ];
            }
        }

        (new CinemaProductStatus())->insertAll($insert);
    }


    public function updateEntityDayPriceStatus($entityId,$dayJsonStr)
    {
        $insert = [];

        (new CinemaProductModel())->where(['entity_id'=>$entityId])->column('');

        $arr = json_decode($dayJsonStr);

        foreach ($arr as $key => $value){
            $insert[] = [
                'entity_id' => $entityId,
                'date'      => strtotime($value['date']),
                'status'    => 0,
            ];
        }

        (new CinemaProductStatus())->insertAll($insert);
    }

/**********************************************************************************/

    //获取自定义字段
    public function getFieldList(ProductFieldImpl $impl,$productId)
    {
        $model = new CinemaProductField();

        $type = $impl->getFieldType();

        $data = $model->where(['product_id'=>$productId,'type'=>$type])->select();

        $return = [];

        foreach ($data as $k => $v){
            $return[$v['product_field_id']] = $v;
        }

        return $return;
    }

    //批量新增
    public function insertField(ProductFieldImpl $impl,$data,$productId)
    {
        $insert = [];

        $type = $impl->getFieldType();

        foreach ($data as $key => $value){
            $insert[] = [
                'product_id'      => $productId,
                'product_field_id' => $value['product_field_id'],
                'type'      => $type,
                'name' => $value['name'],
                'value' => $value['value'],
            ];
        }
        $level = new CinemaProductField();

        $level->insertAll($insert);
    }

    //批量删除
    public function deleteField(ProductFieldImpl $impl,$productId,$ids = null)
    {
        $model = new CinemaProductField();

        if (is_string($ids)){
            $model->where(['id'=>$ids,'product_id'=>$productId,'type'=>$impl->getFieldType()])->delete();
        }else if(!$ids){
            $model->where(['product_id'=>$productId,'type'=>$impl->getFieldType()])->delete();
        }else{
            $model->whereIn('id',$ids)->where(['product_id'=>$productId,'type'=>$impl->getFieldType()])->delete();
        }
    }

/**********************************************************************************/

    //判断产品是否可用
    public function checkProductStatus($productId)
    {
        $product =(new  CinemaProductModel())->receptionShowData()->find($productId);

        if (!$product) return false;

        return $product;

    }

}