<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\model\Permission;
use app\common\typeCode\permission\B;

class index
{
    public function index()
    {
        echo (new Permission())->getList((new B()));
    }
}
