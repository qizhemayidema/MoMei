<?php
declare (strict_types=1);

namespace app\api\controller;

use app\common\model\UserShopping;
use app\common\service\CinemaProduct;
use app\common\service\Manager;
use app\common\service\ProductRule;
use think\exception\ValidateException;
use think\Request;
use think\Validate;

class Shopping extends Base
{
    //添加多个商品到购物车
    public function add(Request $request)
    {
        $user = $this->userInfo;

        $post = $request->post();

        $rules = [
            'product_ids' => 'require',      //多个利用逗号分隔
            'start_time' => 'require',      //开始时间
            'end_time' => 'require',      //结束时间
        ];

        $validate = new Validate();

        $validate->rule($rules);

        if (!$validate->check($post)) {
            return json(['code' => 0, 'msg' => $validate->getError()]);
        }

        //检查用户是否拥有互动权限
        $check = $this->checkUserWriteAuth($user);
        if ($check['code'] == 0) {
            return json($check);
        }

        try {
            //计算每日的时间戳
            $startTime = strtotime(date('Y-m-d', (int)$post['start_time']));
            $endTime = strtotime(date('Y-m-d', (int)$post['end_time']));
            //转换数组
            $productIds = explode(',', $post['product_ids']);

            $productRuleService = new ProductRule();
            $productService = new CinemaProduct();
            $shopping = new UserShopping();

            $insert = [];

            foreach ($productIds as $k => $v) {
                $product = $productService->checkProductStatus($v);

                if (!$product) throw new ValidateException('产品不存在,请刷新后重试');

                //获取产品规则
                $rule = $productRuleService->getByCateId($product['cate_id']);

                if (!$rule) throw new ValidateException('产品不存在,请刷新后重试');

                $insert[] = [
                    'user_id' => $user['id'],
                    'cinema_id' => $product['cinema_id'],
                    'screen_id' => $product['screen_id'],
                    'product_id' => $product['id'],
                    'cate_id' => $product['cate_id'],
                    'cinema_name' => $product['cinema_name'],
                    'screen_name' => $product['screen_name'],
                    'product_name' => $product['entity_name'],
                    'cate_name' => $product['cate_name'],
                    'sum_unit' => $rule['sum_unit'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'create_time' => time(),
                ];

            }

            $shopping->insertAll($insert);

        } catch (ValidateException $e) {
            return join(['code' => 0, 'msg' => $e->getError()]);
        } catch (\Exception $e) {
            return join(['code' => 0, 'msg' => $e->getMessage()]);
        }

        return json(['code' => 1, 'msg' => 'success']);


    }

    //删除多个购物车中的商品
    public function delete(Request $request)
    {
        $user = $this->userInfo;

        $delete = $request->delete();

        $rule = [
            'type',  // shopping or cinema
            'ids',
        ];

        return json($delete);

        //检查用户是否拥有互动权限
        $check = $this->checkUserWriteAuth($user);
        if ($check['code'] == 0) {
            return json($check);
        }

        $field = $delete['type'] == 'shopping' ? 'id' : 'cinema_id';

        $ids = $delete['ids'];

        (new UserShopping())->where(['user_id' => $user['id']])->whereIn($field, $ids)->delete();

        return json(['code' => 1, 'msg' => 'success']);
    }

    //获取购物车列表
    public function getList(Request $request)
    {
        $user = $this->userInfo;

        $rule = [
            'page' => 'require',
            'length' => 'require',
            'area_id' => '',
        ];

        $get = $request->get();

        $startPage = $get['page'] * $get['length'] - $get['length'];

        $length = $get['length'];


        $model = new UserShopping();

        $handler = $model->alias('shopping');

        //筛选条件
        if (isset($get['area_id']) && $get['area_id']) {
            $handler = $handler->join('manager_info info', 'shopping.cinema_id = info.master_user_id')
                ->where(['info.city_id' => $get['area_id']]);
        }

        $res = $handler->where(['shopping.user_id' => $user['id']])->group('shopping.cinema_id')
            ->field('shopping.cinema_id,shopping.sum_unit,shopping.start_time,shopping.end_time')
            ->limit((int)$startPage, (int)$length)->select();

        foreach ($res as $k => $v) {
            $temp = $model->where(['cinema_id' => $v['cinema_id']])
                ->group('product_id')
                ->field('product_name,count(product_id) sum,sum_unit')->select();
            $res[$k]['product'] = $temp;
        }
        return json(['code' => 1, 'msg' => $res]);


    }

    //获取详情
    public function getInfo(Request $request)
    {
        $user = $this->userInfo;
        //获取影院信息
        $cinemaId = $request->get('cinema_id');

        $info = (new Manager())->getInfoByGroupCode($cinemaId);

        //获取产品数据
        $product = (new UserShopping())->where(['cinema_id' => $cinemaId,'user_id'=>$user['id']])
            ->group('product_id')
            ->field('product_id,product_name,count(product_id) sum,sum_unit')->select()->toArray();

        $data = [
            'cinema_name' => $info['name'],
            'ent_name' => $info['ent_name'],
            'duty' => $info['duty'],
            'duty_phone' => $info['duty_tel'],
            'service_phone' => $info['service_phone'],
            'product'   => $product,
        ];

        return json(['code'=>1,'msg'=>'success','data'=>$data]);
    }
}
