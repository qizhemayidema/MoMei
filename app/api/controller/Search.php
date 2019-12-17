<?php
declare (strict_types=1);

namespace app\api\controller;

use app\common\model\CategoryObjHaveAttr as CategoryObjHaveAttrModel;
use app\common\model\CinemaProduct as CinemaProductEntityModel;
use app\common\model\CinemaProductStatus as CinemaProductEntityStatusModel;
use app\common\model\ManagerInfo as ManagerInfoModel;
use app\common\model\CinemaScreen as CinemaScreenModel;
use app\common\model\Manager as ManagerModel;
use app\common\model\Area as AreaModel;
use app\common\service\Cinema as CinemaService;
use think\Request;

class Search
{
    public function index(Request $request)
    {
        $get = $request->get();

        /**
         * SELECT
         * entity.id
         * FROM
         * base_cinema_product_entity entity
         * INNER JOIN base_category_obj_have_attr attr ON attr.object_id = entity.cinema_id
         * AND attr.type = 1
         * INNER JOIN base_manager cinema ON entity.cinema_id = cinema.id
         * AND cinema.type = 4
         * INNER JOIN base_manager_info cinema_info ON cinema_info.id = cinema.info_id
         * WHERE
         * entity.id IN (
         * SELECT
         * entity_id
         * FROM
         * base_cinema_product_entity_status
         * WHERE
         * date IN (
         * 1622217600,
         * 1622390400,
         * 1622649600
         * )
         * AND STATUS = 0
         * GROUP BY
         * entity_id
         * )
         * AND entity.cate_id IN (1, 2, 3)
         * AND attr.attr_id IN (1, 2, 3)
         * AND cinema_info.city_id IN(1,2,3);
         */
        $rules = [
            'start_time' => 'require',   //开始时间
            'end_time' => 'require',   //结束时间
//            'product_cate_ids'   => 'require',   //产品分类ids
//            'cinema_attr_ids'   => 'require',    //影院筛选条件ids
            'city_ids' => 'require',//  城市id 数组
            'city_attr_ids' => 'require',// 城市筛选条件attr ids
        ];

        //计算每日的时间戳
        $startTime = $get['start_time'];

        $date = [];

        while ($startTime <= $get['end_time']) {
            $date[] = $startTime;

            $startTime += 86400;
        }

        //初始化选择的城市id
        $selectCityIds = $get['city_ids'] ?? [];
        //初始化选择的城市属性
        $selectCityAttrIds = $get['city_attr_ids'] ?? [];
        //初始化选择分类
        $cateIds = $get['product_cate_ids'] ?? [];
        //初始化影院筛选条件
        $attrIds = $get['cinema_attr_ids'] ?? [];


        $entityModel = new CinemaProductEntityModel();
        $areaModel = new AreaModel();
        $haveAttrModel = new CategoryObjHaveAttrModel();
        $managerModel = new ManagerModel();
        $cinemaScreenModel = new CinemaScreenModel();
        $managerInfoModel = new ManagerInfoModel();


        $cinemaService = new CinemaService();

        //首先根据 city_ids 和 city_attr_ids 筛选出 符合条件的城市id
        $cityTempIds = $haveAttrModel->where(['type' => 2])->whereIn('attr_id', $selectCityAttrIds)->column('object_id');

        //查询符合地区条件的城市ids
        $cityIds = array_unique(array_merge($cityTempIds, $selectCityIds));


        /**
         * 此处为查询具体商品的地方
         * $handler = $entityModel->alias('entity')
         * ->join('cinema_product product','product.id = entity.product_id')
         * ->join('category_obj_have_attr attr','attr.object_id = entity.cinema_id and attr.type = 1')
         * ->join('manager cinema','entity.cinema_id = cinema.id and cinema.type = 4')
         * ->join('manager_info info','info.id = cinema.info_id')
         * ->whereIn('entity.id',function($query) use ($date){
         * $query->table('base_cinema_product_entity_status')->field('entity_id')->whereIn('date',$date)
         * ->where('status',0)->group('entity_id')->select();
         * })
         * ->where(['cinema.status'=>1,'cinema.delete_time'=>0])
         * ->where(['entity.status'=>1,'entity.delete_time'=>0])
         * ->where(['product.status'=>1,'product.delete_time'=>0]);
         *
         * if ($cateIds) $handler = $handler->whereIn('entity.cate_id',$cateIds);
         * if ($attrIds) $handler = $handler->whereIn('attr.attr_id',$attrIds);
         * if ($cityIds) $handler = $handler->whereIn('info.city_id',$cityIds);
         *
         * echo $handler->buildSql(true);
         *
         * [
         *      city_id,
         *      city_name,
         *      cinema => [
         *
         *      ],
         * ]
         */

        $handler = $entityModel->alias('entity')
            ->join('cinema_product product', 'product.id = entity.product_id')
            ->join('category_obj_have_attr attr', 'attr.object_id = entity.cinema_id and attr.type = 1')
            ->join('manager cinema', 'entity.cinema_id = cinema.id and cinema.type = 4')
            ->join('manager_info info', 'info.id = cinema.info_id')
            ->whereIn('entity.id', function ($query) use ($date) {
                $query->table('base_cinema_product_entity_status')->field('entity_id')->whereIn('date', $date)
                    ->where('status', 0)->group('entity_id')->select();
            })
            ->where(['cinema.status' => 1, 'cinema.delete_time' => 0])
            ->where(['entity.status' => 1, 'entity.delete_time' => 0])
            ->where(['product.status' => 1, 'product.delete_time' => 0]);

        if ($cateIds) $handler = $handler->whereIn('entity.cate_id', $cateIds);
        if ($attrIds) $handler = $handler->whereIn('attr.attr_id', $attrIds);
        if ($cityIds) $handler = $handler->whereIn('info.city_id', $cityIds);

        $handler = $handler->field('entity.cinema_id,entity.id entity_id,entity.entity_name,entity.product_id,attr.attr_value');

        $handler = $handler->field('entity.screen_id,entity.screen_name');

        $handler = $handler->field('entity.cate_id,entity.cate_name');

        $handler = $handler->field('info.city_id,info.city,info.name,info.master_user_id');

        $result = $handler->select()->toArray();

        $return = [];

        $cityTemp = [];

        $cinemaTemp = [];

        $productTemp = [];

        $screenTemp = [];

        $entityTemp = [];


        $result = array_unique($result,SORT_REGULAR);


        foreach ($result as $k => $v){
            //组装实体产品 key为产品id 可能有重复数据
            $entityTemp[$v['product_id']][] = [
                'id'     => $v['entity_id'],
                'name'   => $v['entity_name'],
            ];
            //组装影厅数据 key为 产品id 可能有重复数据
            $screenTemp[$v['product_id']][] = [
                'id'     => $v['screen_id'],
                'name'   => $v['screen_name'],
            ];
            //获取产品 key为 产品分类id 可能有重复数据
            $productTemp[$v['cate_id']][] = [
                'id'     => $v['screen_id'],
                'name'   => $v['screen_name'],
            ];
        }




        //组装城市数据
        foreach ($result as $k => $v) {

            $cityTemp[] = [
                'id' => $v['city_id'],
                'name' => $v['city'],
            ];


        }



        //组装影院数据

        //组装每个影院产品数据
        return json($screenTemp);

        //组装影厅数据


        //组装影院资源分类数据

        //组装实体类数据


//        print_r($handler->select()->toArray());die;
        $result = array_unique($handler->select()->toArray(), SORT_REGULAR);      //如果查询实际商品 不必去重

        //计算影院总量
        $cinema = array_unique(array_column($result, 'cinema_id'), SORT_REGULAR);

        //计算影厅总量
        $screen = array_unique(array_column($result, 'screen_id'), SORT_REGULAR);

        foreach ($screen as $k => $v) {
            if ($v == 0) unset($screen[$k]);
        }

        //计算每座城市的影院数量
        $city = [];

        foreach ($result as $key => $value) {

            if (!isset($city[$value['city_id']])) {
                $city[$value['city_id']] = [
                    'city_id' => $value['city_id'],
                    'city_name' => $value['city'],
                    'sum' => [$value['master_user_id'] => $value['master_user_id']],
                ];
            } else {
                if (!isset($city[$value['city_id']]['sum'][$value['master_user_id']])) {
                    $city[$value['city_id']]['sum'][$value['master_user_id']] = $value['master_user_id'];
                }
            }
        }

        return json(['code' => 1, 'msg' => 'success', 'data' => [
            'cinema_sum' => count($cinema),
            'screen_sum' => count($screen),
            'city' => $city,
        ]]);
    }
}
