<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Category;
use think\Request;
use app\common\typeCode\cate\Product as CateProductTypeCode;

class Product
{
    public function getCate()
    {
        $typeCode = new CateProductTypeCode();

        $data = (new Category())->getList($typeCode);

        return json(['code'=>1,'msg'=>'success','data'=>$data]);
    }
}
