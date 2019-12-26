<?php
declare (strict_types = 1);

namespace app\aAdmin\controller;

use think\Request;

class AUser extends Base
{
    public function info(Request $request)
    {
        $id = $request->param('id');

        return (new \app\common\controller\AUser())->getInfoPage($id);
    }
}
