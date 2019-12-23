<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Category;
use app\common\service\Manager;
use app\common\typeCode\cate\CinemaLevel;
use think\Request;

class Cinema
{
    public function getInfo(Request $request)
    {
        $cinemaId = $request->get('cinema_id');

        $service = new Manager((new \app\common\typeCode\manager\Cinema()));

    }

    //获取影院筛选条件
    public function getCondition()
    {
        $category = new Category();
        $typeCode = new CinemaLevel();

        $data = $category->getList($typeCode);

        foreach ($data as $key => $value){
            $data[$key]['children'] = $category->getAttrList($value['id']);
        }
        return json(['code'=>1,'msg'=>'success','data'=>$data]);

    }
}
