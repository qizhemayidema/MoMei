<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\service\Category;
use app\common\service\CinemaProduct;
use app\common\service\Manager;
use think\Request;
use app\common\typeCode\cate\Product as CateProductTypeCode;

class Product
{
    public function getCate()
    {
        $typeCode = new CateProductTypeCode();

        $data = (new Category())->getList($typeCode);

        return json(['code'=>1,'msg'=>'success','data'=>$data]);
    }

    /**
     * 产品详情
     * @param Request $request
     * $data 10/1/2020 上午10:37
     */
    public function details(Request $request)
    {
        $id = $request->get('id');

        if(!$id) return json(['code'=>0,'msg'=>'参数异常']);

        //产品数据
        $cinemaPService = new CinemaProduct();
        $data = $cinemaPService->getDetails($id);
        if(empty($data)) return json(['code'=>0,'msg'=>'产品不存在或已下架']);
        $data = $data->toArray();

        //查询出影院名称
//        $cinema_name = (new Manager())->getInfo($data['cinema_id']);
        $result['code'] = 1;
        $result['msg'] = 'success';
        $result['data']['cinema_name'] = $data['cinema_name'];  //影院名称
        $result['data']['screen_name'] = $data['screen_name'];  //影厅名称
        $result['data']['entity_name'] = $data['entity_name'];  //实体名称
        $result['data']['cate_name'] = $data['cate_name'];      //分类名称
        $result['data']['price_everyday'] = $data['price_everyday'];  //日均价格
        $result['data']['price_discount_everyday'] = $data['price_discount_everyday'];  //日均优惠价格
        $result['data']['price_month'] = $data['price_month'];  //包月价格
        $result['data']['price_discount_month'] = $data['price_discount_month'];  //包月月优惠价格
        $result['data']['price_year'] = $data['price_year'];   //包年价格
        $result['data']['price_discount_year'] = $data['price_discount_year'];   //年优惠价格
        $result['data']['desc'] = $data['desc'];   //产品介绍
        $result['data']['pic'] = $data['pic'];   //产品封面图
        $result['data']['roll_pic'] = explode(',',$data['roll_pic']);   //产品轮播图

        $price_json = (new CinemaProduct())->productCalendar($data['id'],$data['price_json']);

        $result['data']['price_day'] = json_encode($price_json);   //每日价格 json 格式

        return json($result);
    }
}
