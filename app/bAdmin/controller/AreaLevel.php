<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/27
 * Time: 9:30
 */
declare (strict_types = 1);

namespace app\bAdmin\controller;


use app\common\service\Area;
use think\facade\View;

class AreaLevel extends Base
{
    public function index()
    {
        try{
            //查询全部的省
            $pidResult = (new Area())->getListByPId(0,true);
            View::assign('pidResult',$pidResult);

            return view('area_level/index');
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}