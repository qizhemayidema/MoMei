<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\common\tool\Session;
use app\common\tool\Upload;
use think\facade\Db;
use think\facade\View;
use think\Request;
use app\common\service\Order as OrderServer;
use app\common\service\OrderOtherPay as OrderOtherPayServer;
use app\common\service\OrderPayStages as OrderPayStagesServer;
use think\Validate;

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

    public function update(Request $request)
    {
        Db::startTrans();
        try{
            $user = (new Session())->getData();
            $data = $request->post();

            if(!isset($_FILES['pic'])) throw new \Exception('请先上传合同附件');

            $validate = new Validate();
            $rules = Array(
                'id|id'=>'require',
                'agreement_code|合同编号'=>'require',
                'extra|额外费用'=>'require',
                'all_price|产品总金额'=>'require',
                'agreement_code|订单总金额'=>'require',
                'pay_type|支付类型'=>'require',
                'money_type|付款类型'=>'require',
//                '__token__'     => 'token',
            );
            $validate->rule($rules);
            $checkResult  = $validate->check($data);
            if(!$checkResult){
                throw new \Exception($validate->getError());
            }

            //查询订单
            $OrderServer = new OrderServer();
            $orderData = $OrderServer->get($data['id']);

            //上传附件
            $picUrl = (new Upload())->uploadOneFile('/order','pic');
            if($picUrl['code']!=1) throw new \Exception('附件上传失败');
            $data['agreement'] = $picUrl['msg'];

            //额外费用数据生成
            $other_price = 0;
            if($data['extra']==2){  //有额外费用
                $extraName = $data['extra_name'];
                $extraPrice = $data['extra_price'];

                $orderPriceAddData = [];
                for($a = 0;$a<count($extraName); $a++){
                    if(!isset($extraName[$a]) || !isset($extraPrice[$a]) || $extraName[$a]=='' || $extraPrice[$a]=='' ){
                        throw new \Exception('请把额外费用输入完整');
                    }
                    $orderPriceAddData[] = [
                        'order_id' =>$orderData['id'],
                        'order_code' =>$orderData['order_sn'],
                        'agreement_code' =>$data['agreement_code'],
                        'name' =>$extraName[$a],
                        'price' =>$extraPrice[$a],
                    ];
                    $other_price+=$extraPrice[$a];
                }

                //增加额外费用的数据
                $otherRes = (new OrderOtherPayServer())->addAll($orderPriceAddData);
                if($otherRes!=count($extraName)) throw new \Exception('额外费用生成失败');
            }

            //分期的期数数据
            if($data['money_type']==2){  //分期
                $periodsTime = $data['periods_time'];
                $periodsPrice = $data['periods_price'];

                $periodsPriceAll = 0;
                $orderPeriodsAddData = [];
                for($b=0;$b<count($periodsTime);$b++){
                    if(!isset($periodsTime[$b]) || !isset($periodsPrice[$b]) || $periodsTime[$b]=='' || $periodsPrice[$b]=='' ){
                        throw new \Exception('请把期数数据输入完整');
                    }
                    $orderPeriodsAddData[] = [
                        'order_id' => $orderData['id'],
                        'order_code' => $orderData['order_sn'],
                        'agreement_code' => $data['agreement_code'],
                        'number' => $b+1,
                        'pay_price' => $periodsPrice[$b],
                        'pay_time' => strtotime($periodsTime[$b]),
                    ];
                    $periodsPriceAll+=$periodsPrice[$b];
                }
                if($periodsPriceAll!=$data['price']) throw new \Exception('订单总金额和分期后支付的金额不一致');
                //增加分期表的数据
                $orderPeriodsAddDataRes = (new OrderPayStagesServer())->addAll($orderPeriodsAddData);
                if($orderPeriodsAddDataRes!=count($periodsTime)) throw new \Exception('分期数据生成失败');
            }


            //修改订单信息
            $data['other_price'] = $other_price;
            $orderRes =$OrderServer->updateData($data,$user['group_code']);

            if(!$orderRes) throw new \Exception('修改失败');

            Db::commit();
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            Db::rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function info(Request $request)
    {
        $user = (new Session())->getData();

        $id = $request->get('id');

        //查询订单信息
        $OrderServer = new OrderServer();
        $data = $OrderServer->setGroupCode($user['group_code'])->getOneData($id);

        //查询该订单有没有额外费用
        $otherPayRes = (new OrderOtherPayServer())->getList($id);

        //分期
        $stagesRes = [];
        if($data[0]['money_type']==2){ //分期
            $stagesRes = (new OrderPayStagesServer())->getList($id);
        }

        View::assign('data',$data);

        View::assign('otherPayRes',$otherPayRes);

        View::assign('stagesRes',$stagesRes);

        return view();
    }
}
