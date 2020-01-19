<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\facade\Db;
use think\Request;
use app\common\service\UserShopping as UserShoppingServer;
use app\common\service\Manager as ManagerServer;
use app\common\typeCode\manager\Cinema as CinemaDesc;
use app\common\service\Order as OrderServer;
use app\common\service\CinemaProduct as CinemaProductServer;
use app\common\service\Ordervice as OrderviceServer;
use app\common\service\CinemaProductStatus as CinemaProductStatusServer;
use app\common\service\Category as CategoryServer;
class Order extends Base
{
    public function makeNewOrder(Request $request)
    {
        Db::startTrans();
        try{
            $user = $this->userInfo;

            //检查用户是否拥有互动权限
            $check = $this->checkUserWriteAuth($user);
            if ($check['code'] == 0) {
                return json($check);
            }

            $post = $request->post();

            if(!isset($post['product_data'])) throw new \Exception('请选择产品信息');

            $productData = json_decode($post['product_data'],true);

            if(empty($productData)) throw new \Exception('请选择产品信息');

            $cartIds = implode(',',array_column($productData,'cart_ids'));

            $UserShoppingServer = new UserShoppingServer();

            $cartData = $UserShoppingServer->getDataByInIds($user['id'],$cartIds); //查出购物车中的这些商品

            if(empty($cartData)) throw new \Exception('请进行正确操作');

            $cartDataKeyId = array_column($cartData,NULL,'id');  //购物车的主键id当下标

            $cinemaIds = array_column($cartData,'cinema_id');   //获取这些产品的影院ID

            $cinemas = (new ManagerServer(new CinemaDesc()))->setWhereIn('manager.group_code',$cinemaIds)->getInfoList()->toArray();  //获取这些产品的影院

            $cinemasKeyGroupCode = array_column($cinemas,NULL,'group_code');   //影院的coe作为下标  主要作用是看下单时该影院有没有删除 或冻结

            $productDataInfo = (new CinemaProductServer())->getListByInIds(array_column($cartData,'product_id'));   //查询全部的要下单的产品在产品表中的数据

            $productDataInfoKeyId = array_column($productDataInfo,NULL,'id');

            $CategoryServer = new CategoryServer();

            $cateProductRuleRes = $CategoryServer->getCateProductRule();  //获取全部产品类别的规则

            $cateProductRuleResKeyId = array_column($cateProductRuleRes,NULL,'id');

            $OrderServer = new OrderServer();
            foreach ($productData as $productDataKey=>$productDataValue){
                if($productDataValue['cart_ids']!=''){
                    $addOrderData['user_id'] =$user['id'];
                    $addOrderData['order_sn'] =$this->getOrderSn();
                    $addOrderData['user_name'] =$user['ent_name'];
                    $addOrderData['user_bus_license'] =$user['ent_license_bus_license'];
                    $addOrderData['user_tel'] =$user['phone'];
                    $addOrderData['user_province'] =$user['ent_province'];
                    $addOrderData['user_province_id'] =$user['ent_province_id'];
                    $addOrderData['user_city'] =$user['ent_city'];
                    $addOrderData['user_city_id'] =$user['ent_city_id'];
                    $addOrderData['user_county'] =$user['ent_county'];
                    $addOrderData['user_county_id'] =$user['ent_county_id'];
                    $addOrderData['user_address'] =$user['ent_address'];
                    $addOrderData['user_contact'] =$user['name'];
                    $addOrderData['user_contact_sex'] =$user['sex'];
                    $addOrderData['user_contact_tel'] =$user['phone'];
                    $productDataValueIds = explode(',',$productDataValue['cart_ids']);
                    if(!isset($cinemasKeyGroupCode[$cartDataKeyId[$productDataValueIds[0]]['cinema_id']]) || empty($cinemasKeyGroupCode[$cartDataKeyId[$productDataValueIds[0]]['cinema_id']])){
                        throw new \Exception($cartDataKeyId[$productDataValueIds[0]]['cinema_name'].'已经下线');  //影院冻结 或者已经删除
                    }
                    $cinemaInfo = $cinemasKeyGroupCode[$cartDataKeyId[$productDataValueIds[0]]['cinema_id']];
                    $addOrderData['cinema_id'] = $cartDataKeyId[$productDataValueIds[0]]['cinema_id'];
                    $addOrderData['cinema_name'] = $cartDataKeyId[$productDataValueIds[0]]['cinema_name'];
                    $addOrderData['cinema_credit_code'] = $cinemaInfo['credit_code'];
                    $addOrderData['cinema_bus_license'] = $cinemaInfo['bus_license'];
                    $addOrderData['cinema_tel'] = $cinemaInfo['tel'];
                    $addOrderData['cinema_province'] = $cinemaInfo['province'];
                    $addOrderData['cinema_city'] = $cinemaInfo['city'];
                    $addOrderData['cinema_county'] = $cinemaInfo['county'];
                    $addOrderData['cinema_address'] = $cinemaInfo['address'];
                    $addOrderData['cinema_contact'] = $cinemaInfo['contact'];
                    $addOrderData['cinema_contact_sex'] = $cinemaInfo['contact_sex'];
                    $addOrderData['cinema_contact_tel'] = $cinemaInfo['contact_tel'];
                    $addOrderData['create_time'] = time();
                    $addOrderData['yw_start_time'] = $productDataValue['start_time'];
                    $addOrderData['yw_end_time'] = $productDataValue['end_time'];

                    $orderId = $OrderServer->addDataId($addOrderData);

                    if(!$orderId) throw new \Exception('订单生成失败');

                    //组装生成订单副表的记录
                    $addOrdervice = [];
                    $productIds = '';
                    foreach ($productDataValueIds as $productDataValueIdsKey=>$productDataValueIdsValue){  //循环得到该订单中有多少个产品
                        $selfCart = $cartDataKeyId[$productDataValueIdsValue];  //当前购物车的那条数据

                        if(!isset($productDataInfoKeyId[$selfCart['product_id']]) || empty($productDataInfoKeyId[$selfCart['product_id']])){
                            throw new \Exception($cartDataKeyId[$productDataValueIds[0]]['cinema_name'].'--'.$selfCart['product_name'].'已经下架');
                        }
                        $productId = $selfCart['product_id'];
                        $productIds.=$productId.",";
                        $productSelfData = $productDataInfoKeyId[$selfCart['product_id']];         //购物车产品对应的产品表的数据

                        //判断下单时候  得到该产品的规则 产品是否影厅内
                        $v_screen_id = 0;
                        $v_screen_name = '';
                        //是影厅内的
                        if(isset($cateProductRuleResKeyId[$productSelfData['cate_id']]) && !empty($cateProductRuleResKeyId[$productSelfData['cate_id']]) &&  $cateProductRuleResKeyId[$productSelfData['cate_id']]['is_screen']==1){
                            $v_screen_id = $productSelfData['screen_id'];
                            $v_screen_name = $productSelfData['screen_name'];
                        }


                        $addOrdervice[] = [
                            'o_id'=>$orderId,
                            'v_product_id'=>$productId,
                            'v_product_name'=>$productSelfData['entity_name'],
                            'v_screen_id'=>$v_screen_id,
                            'v_screen_name'=>$v_screen_name,
                            'v_product_json'=>json_encode($productSelfData),
                            'v_price_json'=>$productSelfData['price_json'],
                            'v_price_month'=>$productSelfData['price_month'],
                            'v_price_year'=>$productSelfData['price_year'],
                            'v_price_everyday'=>$productSelfData['price_everyday'],
                            'v_price_discount'=>$productSelfData['price_discount'],
                            'v_price_discount_month'=>$productSelfData['price_discount_month'],
                            'v_price_discount_year'=>$productSelfData['price_discount_year'],
                            'v_price_discount_everyday'=>$productSelfData['price_discount_everyday'],
                        ];

                        $productCalendarDayRes = (new CinemaProductStatusServer())->productCalendarDay($productDataValue['start_time'],$productDataValue['end_time'],$productId);
                        if($productCalendarDayRes<=0) throw new \Exception($cartDataKeyId[$productDataValueIds[0]]['cinema_name'].'--'.$selfCart['product_name'].'已有档期');
                    }

                    $addOrderviceRes = (new OrderviceServer())->addAllData($addOrdervice);

                    if(!$addOrderviceRes) throw new \Exception('订单信息生成失败');

                    //修改产品的档期
                    $productStatus = (new CinemaProductStatusServer())->updateByTimes($productDataValue['start_time'],$productDataValue['end_time'],$productIds,2);
                    if(!$productStatus) throw new \Exception('产品档期修改失败');
                }
            }
            //删除购物车的数据
            $delShopping = $UserShoppingServer->deleteByIds($user['id'],$cartIds);

            if(!$delShopping) throw new \Exception('生成订单失败');

            Db::commit();
            return json(['code'=>1,'msg'=>'success']);
        }catch (\Exception $e){
            Db::rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
    }

    public function getOrderSn() {
        $time =  (string)time();
        /* 选择一个随机的方案 */
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 't', 'U', 'V', 'W', 'X', 'Y', 'Z');
        return $yCode[intval(date('Y')) - 2016] . strtoupper(dechex(date('m'))) . date('d') . 'A' . substr($time,0, -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    }
}
