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
use app\common\typeCode\manager\Cinema as CinemaTypeDesc;
use app\common\service\Manager as ManagerService;
use app\Request;
use think\facade\View;

class BoxOfficeIncome extends Base
{
    public function index(Request $request)
    {
        $cinemaId = $request->param('cinemaid') ?? '';

        $times = $request->param('times') ?? '';

        $info = (new Session())->getData();

        $boxOfficeService=  new BoxOfficeService();

        $field = '';

        $managerService = new ManagerService(new CinemaTypeDesc());

        if($info['type']==2){  // 院线
            $boxOfficeService = $boxOfficeService->setWhere('yuan_id',$info['group_code']);
            $field = 'yuan_id';
            $type = 'yuan';
        }elseif ($info['type']==3){   //影投
            $boxOfficeService = $boxOfficeService->setWhere('tou_id',$info['group_code']);
            $field = 'tou_id';
            $type = 'ying';
        }

        if ($cinemaId){
            $type = 'cinema';
        }

        $data = $boxOfficeService->order('create_time','desc')->pageLength(15)->getList($cinemaId,$times);

        $allSum = $boxOfficeService->getSum($type,$cinemaId ? $cinemaId : $info['group_code']);

        $cinemaData = $managerService->setWhere('info',$field,$info['group_code'])->showType(true)->getInfoList();

        View::assign(['cinemaid'=>$cinemaId,'times'=>$times]);

        View::assign('cinemaData',$cinemaData);

        View::assign('data',$data);

        View::assign('all_sum',$allSum);

        return view();
    }
}