<?php
declare (strict_types = 1);

namespace app\bAdmin\controller;

use app\common\model\Area;
use think\Request;
use app\common\service\User;
use app\common\model\User as UserModel;
use think\facade\View;

class CUserAuth extends Base
{
    public  function index()
    {
        $list = (new UserModel())->backgroundShowData()->where(['license_status'=>2])->order('id','desc')->paginate(15);

        View::assign('list',$list);

        return view();
    }

    public function changeAuthStatus(Request $request)
    {
        $id = $request->post('id');

        $status = $request->post('status');

        (new User())->changeAuthStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);


    }

    public function info(Request $request)
    {
        $info = (new UserModel())->find($request->param(['id']));

//        dump($info->toArray());die;

        $entBusArea = (new Area())->whereIn('id',$info['ent_bus_area_ids'])->select();

        View::assign('info',$info);
        View::assign('bus_area',$entBusArea);

        return view();
    }
}
