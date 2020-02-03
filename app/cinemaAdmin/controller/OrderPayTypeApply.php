<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\common\service\OrderPayStages as OrderPayStagesService;
use app\Request;
use think\facade\View;

class OrderPayTypeApply extends Base
{
    public function index()
    {
        //查询那些期数申请了支付方式
        $data = (new OrderPayStagesService())->setPageLength()->getList(false,'2,3');

        View::assign('data',$data);

        return view();
    }

    public function disposeApply(Request $request)
    {
        if($request->isPost()){
            $id = $request->post('id');
            $status = $request->post('status');
            $content = $request->post('content');

            if($id == '' || $status=='' ){
                return json(['code'=>0,'msg'=>'参数错误']);
            }

           if($status==2 && $content=='') return json(['code'=>0,'msg'=>'请填写拒绝原因']);

            $OrderPayStagesService = new OrderPayStagesService();
            $info = $OrderPayStagesService->get($id);


            if($status==1){ //同意
                if($info['apply_pay_type']==2) $updateData['pay_type'] = 1;
                if($info['apply_pay_type']==3) $updateData['pay_type'] = 2;
                $updateData['apply_pay_type'] = 1;
            }else{  //拒绝
                if($info['apply_pay_type']==2) $updateData['apply_pay_type'] = 4;
                if($info['apply_pay_type']==3) $updateData['apply_pay_type'] = 5;
                $updateData['refuse_content'] = $content;
            }
            $result = $OrderPayStagesService->update($id,$updateData);

            if(!$result) return json(['code'=>0,'msg'=>'操作失败']);

            return json(['code'=>1,'msg'=>'success']);
        }
    }
}
