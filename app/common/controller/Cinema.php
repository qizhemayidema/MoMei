<?php
declare (strict_types = 1);

namespace app\common\controller;

use app\common\service\CategoryObjHave;
use app\common\typeCode\cate\CinemaNearby;
use think\Request;
use app\common\service\Manager as Service;
use app\common\service\CategoryObjHaveAttr;
use think\facade\View;

class Cinema
{
    //获取详情页面
    public function getInfoPage($id)
    {
        $service = (new Service(new \app\common\typeCode\manager\Cinema()));

        $user = $service->get($id);

        $info = $service->getInfo($user['info_id']);

        $yingService = (new Service(new \app\common\typeCode\manager\Ying()));
        $yuanService = (new Service(new \app\common\typeCode\manager\Yuan()));

        $yingUser = $yingService->get($info['tou_id']);
        $tou = $yingService->getInfo($yingUser['info_id']);

        $yuanUser =  $yuanService->get($info['yuan_id']);
        $yuan =  $yuanService->getInfo($yuanUser['info_id']);

        $levels = (new CategoryObjHaveAttr(1))->getList($user['group_code']);

        //查询影院周边
        $around = (new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema())))->getList((new CinemaNearby()),$user['group_code']);


        View::assign(compact('user','info','tou','yuan','yingUser','yuanUser','levels','around'));

        return view('common@cinema/info');

//        return
    }
}