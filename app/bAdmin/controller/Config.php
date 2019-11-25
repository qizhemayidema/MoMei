<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\service\Category;
use app\common\typeCode\cate\ABusCate;
use think\Request;

class Config extends Base
{
    public function index()
    {

        try{
            $list = (new \app\common\service\Config())->getList(parent::WEBSITE_CONFIG_PATH);

            return view('config/index');
        }catch(\Exception $e){
            return '操作失误';
        }
    }
}
