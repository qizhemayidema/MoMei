<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Category;
use think\facade\View;
use think\Request;

class ABusCate extends Base
{
    public function index()
    {
        try{
            $data = (new Category())->getABusList();

            View::assign('cate',$data);

            return view('a_bus_cate/index');

        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function add()
    {

    }
}
