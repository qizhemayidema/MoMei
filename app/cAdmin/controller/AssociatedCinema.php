<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 14:01
 */

namespace app\cAdmin\controller;


use app\common\tool\Session;
use app\common\service\AssociatedCinema as AssociatedCinemaService;
use app\Request;
use think\facade\View;

class AssociatedCinema extends Base
{
    public function index()
    {
        $info = (new Session())->getData();

        $data = (new AssociatedCinemaService())->getAssociatedCinemaList($info['type'],$info['id'],15);

        View::assign('data',$data);

        return view();
    }

    public function getDetail(Request $request)
    {
        $cinemaId = $request->param('cinema_id');

        $data = (new AssociatedCinemaService())->getCinema($cinemaId);

        View::assign('data',$data);

        return view();
    }
}