<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/2
 * Time: 17:24
 */

namespace app\common\service;


class CategoryObjHaveAttr
{
    private $type = null;   //1 影院 2 地区

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getList($objId)
    {
        $model = new \app\common\model\CategoryObjHaveAttr();

        return $model->where(['type'=>$this->type,'object_id'=>$objId])->select();

    }

    public function insert($objId,$levels,$options)
    {
        $model = new \app\common\model\CategoryObjHaveAttr();

        $insert = [];
        foreach ($levels as $key => $value)     // key 选项名 id  value 选项名的字符串
        {
            $option = explode('-',$options[$key]);
            $insert[] = [
                'object_id' => $objId,
                'type'   => $this->type,
                'cate_id' => $key,
                'attr_id' => $option[0],
                'cate_name' => $value,
                'attr_value' => $option[1]
            ];
        }

        $model->insertAll($insert);
    }

    //更改 如果不存在则新增
    public function update($objId,$levels,$options)
    {
        $model = new \app\common\model\CategoryObjHaveAttr();

        foreach ($levels as $key => $value)     // key 选项名 id  value 选项名的字符串
        {
            $option = explode('-',$options[$key]);

            $id = $model->where(['object_id'=>$objId,'cate_id'=>$key,'type'=>$this->type])->value('id');

            if ($id){
                $model->where(['id'=>$id])->update([
                    'attr_id' => $option[0],
                    'cate_name' => $value,
                    'attr_value' => $option[1]
                ]);
            }else{
                $model->insert([
                    'object_id' => $objId,
                    'cate_id' => $key,
                    'attr_id' => $option[0],
                    'cate_name' => $value,
                    'attr_value' => $option[1]
                ]);
            }


        }
    }


    public function getIdColumns($objId)
    {
        return (new \app\common\model\CategoryObjHaveAttr())->where(['object_id'=>$objId])->column('attr_id','cate_id');
    }
//    public function update($id)
}