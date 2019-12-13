<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\model\AreaConfig;
use app\common\model\Category;
use app\common\model\CategoryAttr;
use think\Request;

class Test
{
    public function addHot(Request $request)
    {
        $city_ids = $request->post('city_ids');
        (new AreaConfig())->whereIn('city_id',$city_ids)->update(['is_hot'=>1]);
    }

    public function addAttr(Request $request)
    {

        $ids = $request->post('city_ids');
        $cateId = $request->post('cate_id');
        $cateName = (new Category())->where(['id'=>$cateId])->value('name');
        $attrId = $request->post('attr_id');
        $attrName = (new CategoryAttr())->where(['id'=>$attrId])->value('value');

        foreach ($ids as $k => $v){
            $id = (new \app\common\model\CategoryObjHaveAttr())->where(['object_id'=>$v,'cate_id'=>$cateId,'type'=>2])->value('id');

            if ($id){
                (new \app\common\model\CategoryObjHaveAttr())->where(['id'=>$id])->update([
                    'attr_id' => $attrId,
                    'cate_name' => $cateName,
                    'attr_value' =>$attrName
                ]);

            }else{
                (new \app\common\model\CategoryObjHaveAttr())->insert([
                    'object_id' => $v,
                    'cate_id' => $cateId,
                    'attr_id' => $attrId,
                    'cate_name' => $cateName,
                    'attr_value' => $attrName,
                    'type'   => 2,
                ]);
            }
        }
    }
}
