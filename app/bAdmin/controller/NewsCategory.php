<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 14:23
 */

namespace app\bAdmin\controller;

use \app\common\typeCode\NewsCategory\NewsCateGory as CateDesc;
use think\facade\View;

class NewsCategory
{
    public function index()
    {
        try{
            $data = (new \app\common\service\NewsCategory())->getNewsCategoryLists(new CateDesc(),true,15);
            echo "<pre>";
            print_r($data);
            echo "</pre>";

            return View();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}