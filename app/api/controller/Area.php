<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Category;
use app\common\typeCode\cate\AreaLevel;
use think\Request;

class Area
{
    //获取热门城市
    public function getHot()
    {
        $data = (new \app\common\service\AreaConfig())->getList(true);

        $result = [];

        foreach ($data as $key => $value){
            $result[] = [
                'city_id' => $value['city_id'],
                'city_name' => $value['city_name'],
            ];
        }

        return json(['code'=>1,'msg'=>'success','data'=>$result]);
    }

    //获取筛选条件
    public function getCondition()
    {
        $category = new Category();
        $typeCode = new AreaLevel();

        $data = $category->getList($typeCode);

        foreach ($data as $key => $value){
            $data[$key]['children'] = $category->getAttrList($value['id']);
        }
        return json(['code'=>1,'msg'=>'success','data'=>$data]);

    }
}
