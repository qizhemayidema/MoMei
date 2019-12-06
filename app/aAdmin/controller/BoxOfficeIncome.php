<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/4
 * Time: 13:47
 */

namespace app\aAdmin\controller;


use app\common\service\BoxOffice as BoxOfficeService;
use app\common\tool\Session;
use app\common\typeCode\BoxOffice\BoxOfficeIncome as TypeDesc;
use think\facade\View;

class BoxOfficeIncome extends Base
{
    public function index()
    {
        $info = (new Session())->getData();

        $boxOfficeService=  new BoxOfficeService(new TypeDesc());

        if($info['type']==2){  // 院线
            $boxOfficeService = $boxOfficeService->setWhere('yuan_id',$info['group_code']);
        }elseif ($info['type']==3){   //影投
            $boxOfficeService = $boxOfficeService->setWhere('tou_id',$info['group_code']);
        }

        $data = $boxOfficeService->order('create_time','desc')->pageLength(15)->getList();

        View::assign('data',$data);

        return view();
    }
}