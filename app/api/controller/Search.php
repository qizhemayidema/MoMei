<?php
declare (strict_types=1);

namespace app\api\controller;

use app\common\model\CategoryObjHaveAttr as CategoryObjHaveAttrModel;
use app\common\model\CinemaProduct as ProductModel;
use app\common\model\CinemaProductStatus as CinemaProductEntityStatusModel;
use app\common\model\ManagerInfo as ManagerInfoModel;
use app\common\model\CinemaScreen as CinemaScreenModel;
use app\common\model\Manager as ManagerModel;
use app\common\model\Area as AreaModel;
use app\common\service\Category;
use app\common\service\CategoryObjHave;
use app\common\service\CategoryObjHaveAttr;
use app\common\service\Cinema as CinemaService;
use app\common\service\CinemaScreen as CinemaScreenServer;
use app\common\typeCode\cate\CinemaNearby;
use think\Request;

class Search
{
    //首页搜索
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
            'start_time' => 'require',   //开始时间product_cate_ids
            'end_time' => 'require',   //结束时间
            'cate_ids'  => 'require',
            'cinema_attr_ids'   => 'require',    //影院筛选条件ids
            'city_ids' => 'require',//  城市id 数组
            'city_attr_ids' => 'require',// 城市筛选条件attr ids
        ];



        //计算每日的时间戳
        $startTime = strtotime(date('Y-m-d',(int) $get['start_time']));
        $endTime = strtotime(date('Y-m-d',(int) $get['end_time']));

        $date = [];

        while ($startTime <= $endTime) {
            $date[] = $startTime;

            $startTime += 86400;
        }

        //初始化选择的城市id
        $selectCityIds = isset($get['city_ids']) && count($get['city_ids']) ? $get['city_ids'] :  [];
        //初始化选择的城市属性
        $selectCityAttrIds = isset($get['city_attr_ids']) && count($get['city_attr_ids']) ? $get['city_attr_ids']:  [];
        //初始化影院筛选条件
        $attrIds = isset( $get['cinema_attr_ids']) && count( $get['cinema_attr_ids']) ?  $get['cinema_attr_ids'] :  [];
        //初始化分类ids
        $cateIds = isset( $get['cate_ids']) && count( $get['cate_ids']) ?  $get['cate_ids'] :  [];

        $productModel = new ProductModel();
        $haveAttrModel = new CategoryObjHaveAttrModel();


        //首先根据 city_ids 和 city_attr_ids 筛选出 符合条件的城市id
        $cityTempIds = $haveAttrModel->where(['type' => 2])->whereIn('attr_id', $selectCityAttrIds)->column('object_id');

        //查询符合地区条件的城市ids
        $cityIds = array_unique(array_merge($cityTempIds, $selectCityIds));



        $handler = $productModel->alias('product')
            ->join('category cate','cate.id = product.cate_id and cate.type = 6')
            ->join('product_rule rule','cate.id = rule.cate_id')
            ->join('category_obj_have_attr attr', 'attr.object_id = product.cinema_id and attr.type = 1')
            ->join('manager cinema', 'product.cinema_id = cinema.id and cinema.type = 4')
            ->join('manager_info info', 'info.id = cinema.info_id')
            ->whereIn('product.id', function ($query) use ($date) {
                $query->table('base_cinema_product_status')->field('entity_id')->whereIn('date', $date)
                    ->where('status', 0)->group('entity_id')->select();
            })
            ->where(['cinema.status' => 1, 'cinema.delete_time' => 0])
            ->where(['product.status' => 1, 'product.delete_time' => 0]);

        if ($attrIds) $handler = $handler->whereIn('attr.attr_id', $attrIds);
        if ($cityIds) $handler = $handler->whereIn('info.city_id', $cityIds);
        if ($cateIds) $handler = $handler->whereIn('cate.id', $cateIds);

        $handler = $handler->field('product.cinema_id,product.id product_id,product.entity_name product_name,attr.attr_value');

        $handler = $handler->field('product.screen_id,product.screen_name');

        $handler = $handler->field('rule.is_screen');

        $handler = $handler->field('cate.id cate_id,cate.name cate_name,cate.icon cate_icon');

        $handler = $handler->field('attr.attr_value');

        $handler = $handler->field('info.province,info.city_id,info.city,info.county,info.address,info.pics info_pics,info.email info_email,info.name cinema_name,info.master_user_id');

        $handler = $handler->field('info.bus_area,info.seat_sum,info.watch_mv_sum,info.screen_sum');

        $handler = $handler->field('cinema.group_code');

        $result = $handler->select()->toArray();

        $cityTemp = [];

        $cinemaTemp = [];

        $productTemp = [];

        $cateScreenTemp = [];

        $cateTemp = [];

        $cinemaAttrTemp = [];

        $levelTemp = [];

        $result = array_unique($result,SORT_REGULAR);

        //fff
        //获取级别分类列表  这里是需要返回影院的级别的选项
        $cateService = new Category();
        $level = $cateService->getList((new \app\common\typeCode\cate\CinemaLevel()));
        foreach ($level as $key => $value){
            $level[$key]['attr'] =  $cateService->getAttrList($value['id'])->toArray();
        }
        $levelCheck = (new CategoryObjHaveAttr(1))->getList();
        $newLevelCheck = [];
        foreach ($levelCheck as $levelCheckKey=>$levelCheckValue){
            $newLevelCheck[$levelCheckValue['object_id']][$levelCheckValue['cate_id']] = $levelCheckValue['attr_id'];
        }
        //查询平台全部的影厅
        $screenAll = (new CinemaScreenServer())->getListAll()->toArray();
        $newScreenAll = array_column($screenAll,NULL,'id');
        //获取全部的影院的周边的选择
        $cinemaEarByTypeCode = (new CinemaNearby());
        $selectAroundList = (new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema())))->getListAll($cinemaEarByTypeCode)->toArray();
        //fffend

        foreach ($result as $k => $v){
            //组装产品 key为产品id 可能有重复数据
            $productTemp[$v['cinema_id']][$v['cate_id']][$v['screen_id']][] = [
                'id'        => $v['product_id'],
                'name'      => $v['product_name'],
                'is_screen' => $v['is_screen'],
                'screen_id' => $v['screen_id'],
                'cate_id'   => $v['cate_id'],
                'cinema_id' => $v['cinema_id'],
                'city_id'   => $v['city_id'],
                'is_product'   => 'true',
            ];
            //组装影厅数据 key为 产品id 可能有重复数据
            $cateScreenTemp[$v['cinema_id']][$v['cate_id']][] = [
                'id'     => $v['screen_id'],
                'name'   => $v['screen_name'],
                'seat_sum' => isset($newScreenAll[$v['screen_id']]) ? $newScreenAll[$v['screen_id']]['seat_sum'] : 0,
            ];
            //组装分类数据 key为 影院id 可能有重复数据
            $cateTemp[$v['cinema_id']][] = [
                'id'     => $v['cate_id'],
                'name'   => $v['cate_name'],
                'icon'   => $v['cate_icon'],
                'is_screen' => $v['is_screen'],
            ];
            //组装影院的属性值  可能有重复数据
            $cinemaAttrTemp[$v['cinema_id']][] = $v['attr_value'];
            //组装该影院的级别信息
            $cinemaLevel = [];
            if(isset($newLevelCheck[$v['group_code']])){
                foreach ($level as $levelKey=>$levelValue){
                    if(isset($newLevelCheck[$v['group_code']][$levelValue['id']])){
                        foreach ($levelValue['attr'] as $levelKey1=>$levelValue2){
                            if($newLevelCheck[$v['group_code']][$levelValue['id']] == $levelValue2['id']){
                                $cinemaLevel[$levelValue['name']]=$levelValue2['value'];
                            }
                        }
                    }
                }
            }
            $levelTemp[$v['cinema_id']] =$cinemaLevel;
            //组装影院数据 key为城市id 可能有重复数据
            $cinemaTemp[$v['city_id']][] = [
                'id'     => $v['cinema_id'],
                'name'   => $v['cinema_name'],
                'province' => $v['province'],
                'city'      => $v['city'],
                'county'    => $v['county'],
                'bus_area' => $v['bus_area'],
                'address'   => $v['address'],
                'pics'     => $v['info_pics'],
                'email'     => $v['info_email'],
                'screen_sum' => $v['screen_sum'],
                'seat_sum'  => $v['seat_sum'],
                'watch_mv_sum' => $v['watch_mv_sum'],
            ];
            //组装城市数据 无key  可能有重复数据
            $cityTemp[] = [
                'id'    => $v['city_id'],
                'name'  => $v['city'],
            ];
        }

        $cinemaSum = 0;

        $screenSum = 0;

        $cityTemp = array_merge(array_unique($cityTemp,SORT_REGULAR));

        foreach ($cityTemp as $k => $v){        //这层组装影院
            $cityTemp[$k]['cinema'] = array_merge(array_unique($cinemaTemp[$v['id']],SORT_REGULAR));
            //这层组装影院的属性
            foreach ($cityTemp[$k]['cinema'] as $tempKey => $tempValue){
                $cityTemp[$k]['cinema'][$tempKey]['attr'] = array_merge(array_unique($cinemaAttrTemp[$tempValue['id']]));
                if(isset($levelTemp[$tempValue['id']])){    //这里是组装影院的等级名称和值
                    $cityTemp[$k]['cinema'][$tempKey]['level'] = $levelTemp[$tempValue['id']];
                }
                foreach ($selectAroundList as $selectAroundListKey=>$selectAroundListValue) {   //组装影院周边
                    if ($selectAroundListValue['object_id'] == $cityTemp[$k]['cinema'][$tempKey]['id']) {
                        $cityTemp[$k]['cinema'][$tempKey]['rim'][] = $selectAroundListValue['cate_name'];
                    }
                }
            }
            foreach ($cityTemp[$k]['cinema'] as $k1 => $v1){    //这层组装分类
                $cinemaSum ++;
                $cityTemp[$k]['cinema'][$k1]['cate'] = array_merge(array_unique($cateTemp[$v1['id']],SORT_REGULAR));
                foreach ($cityTemp[$k]['cinema'][$k1]['cate']  as $k2 => $v2){  //这层组装影厅 or 产品
                    if ($v2['is_screen'] == 1){     //说明该分类有影厅
                        $cityTemp[$k]['cinema'][$k1]['cate'][$k2]['screen'] = array_merge(array_unique($cateScreenTemp[$v1['id']][$v2['id']],SORT_REGULAR));
                        foreach ($cityTemp[$k]['cinema'][$k1]['cate'][$k2]['screen'] as $k3 => $v3){
                            $screenSum ++;
                            $cityTemp[$k]['cinema'][$k1]['cate'][$k2]['screen'][$k3]['product'] =array_merge(array_unique($productTemp[$v1['id']][$v2['id']][$v3['id']],SORT_REGULAR));
                        }
                    }else{
                        $cityTemp[$k]['cinema'][$k1]['cate'][$k2]['product'] = array_merge(array_unique($productTemp[$v1['id']][$v2['id']][0],SORT_REGULAR));
                    }
                }
            }

        }

        $data = [
            'result'    => $cityTemp,
            'cinema_sum' => $cinemaSum,
            'screen_sum' => $screenSum,
        ];


        return json(['code'=>1,'msg'=>'success','data'=>$data]);
    }
}
