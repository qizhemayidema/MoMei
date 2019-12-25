<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;

class Order
{
    public function makeNewOrder(Request $request)
    {
        $post = $request->post();

        $rules = [

        ];


        //依次判断产品是否合法
    }
}
