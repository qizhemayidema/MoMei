<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 14:01
 */
declare (strict_types = 1);
namespace app\aAdmin\controller;


use app\common\service\AUser;
use app\common\service\Cinema;
use app\common\tool\Session;
use app\Request;
use think\facade\View;

class AssociatedCinema extends Base
{
    public function index()
    {
        $info = (new Session())->getData();

        $data = (new AUser())->getAssociatedCinemaList($info['type'],$info['id'],15);

        View::assign('data',$data);

        return view();
    }

    public function getDetail(Request $request)
    {
        $cinemaId = $request->param('cinema_id');

        $data = (new Cinema())->get($cinemaId);

        View::assign('data',$data);

        return view();
    }
}