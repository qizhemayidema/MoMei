<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;

class Shopping extends Base
{
    public function add(Request $request)
    {
        $user = $this->userInfo;

        $post = $request->post();

        $rules = [
            'product_id'    => 'require',
            ''
        ];


    }

    public function delete(Request $request)
    {

    }
}
