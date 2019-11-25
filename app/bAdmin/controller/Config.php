<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use think\Request;

class Config extends Base
{
    public function index()
    {
        return view('config/index');
    }
}
