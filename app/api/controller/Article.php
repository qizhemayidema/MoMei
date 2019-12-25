<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Category;
use app\common\typeCode\NewsCategory\NewsCateGory;
use think\Request;

//文章列表
class Article
{
    public function getCate()
    {
        $data = (new \app\common\service\NewsCategory())->getNewsCategoryLists((new NewsCateGory()));

        $return = [];

        foreach ($data as $k => $v) {
            if ($k >= 3) break;
            $return[] = $v;
        }
        return json(['code'=>1,'msg'=>'success','data'=>$return]);
    }
}