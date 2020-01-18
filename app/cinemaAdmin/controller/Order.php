<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\common\tool\Session;
use think\facade\View;
use think\Request;
use app\common\service\Order as OrderServer;
class Order extends Base
{
    public function index()
    {
        $user = (new Session())->getData();
        //查询订单
        $OrderServer = new OrderServer();
        $orderData = $OrderServer->setPageLength(15)->setGroupCode($user['group_code'])->getList();

        View::assign('orderData',$orderData);

        return view();
    }

    public function edit(Request $request)
    {
        $user = (new Session())->getData();

        $id = $request->get('id');

        //查询订单信息
        $OrderServer = new OrderServer();
        $data = $OrderServer->setGroupCode($user['group_code'])->getOneData($id);

        View::assign('data',$data);

        return view();
    }
}
