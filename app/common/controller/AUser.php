<?php
declare (strict_types = 1);

namespace app\common\controller;

use think\Request;
use app\common\service\Manager as Service;
use app\common\typeCode\manager\Cinema as CinemaTypeDesc;
use think\facade\View;

class AUser
{
    public function getInfoPage($id)
    {

        $service = (new Service(new CinemaTypeDesc()));

        $user = $service->get($id);

        $info = $service->getInfo($user['info_id']);

        //查询影院关联总数等
        $countResult  = $service->getCinemaAmountCount($user['group_code']);

//        //查询直系影院总数
//        $getLinealCinemaAmountCount = $service->getLinealCinemaAmountCount($info['info_id']);
//
//        View::assign('getLinealCinemaAmountCount',$getLinealCinemaAmountCount);

        View::assign('countResult',$countResult);

        View::assign('user',$user);

        View::assign(['info'=>$info]);

        return view('common@a_user/info');
    }
}
