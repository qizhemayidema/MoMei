<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 14:01
 */
declare (strict_types = 1);
namespace app\aAdmin\controller;


use app\common\service\Manager as ManagerService;
use app\common\tool\Session;
use app\common\typeCode\manager\Cinema as CinemaTypeDesc;
use app\Request;
use think\facade\View;


class AssociatedCinema extends Base
{
    public function index()
    {
        $info = (new Session())->getData();

        //查询属于该资源方下的影院
        $managerService = '';
        $field = '';
        $managerService = new ManagerService(new CinemaTypeDesc());
        if($info['type']==2){  //院线
            $field = 'yuan_id';
        }elseif ($info['type']==3){ //影投
            $field = 'tou_id';
        }

        $data = $managerService->setWhere('info',$field,$info['group_code'])->showType(true)->pageLength()->getList();


        View::assign('data',$data);

        return view();
    }

    public function getDetail(Request $request)
    {
        $id = $request->param('id');

        $service = new ManagerService((new CinemaTypeDesc()));

        //获取数据
        $user = $service->get($id);

        $data = $service->getInfo($user['info_id']);

        View::assign('data',$data);

        return view();
    }
}