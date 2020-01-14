<?php
declare (strict_types=1);

namespace app\api\controller;

use app\common\model\CinemaProduct as CinemaProductModel;
use app\common\model\Category as CateModel;
use app\common\service\Category;
use app\common\service\CategoryObjHave;
use app\common\service\CinemaScreen as CinemaScreenServer;
use app\common\service\ProductRule;
use app\common\typeCode\cate\CinemaNearby;
use app\common\typeCode\cate\Product as ProductCateTypeCode;
use app\common\service\CategoryObjHaveAttr;
use app\common\service\Manager;
use app\common\typeCode\cate\CinemaLevel;
use app\common\service\CinemaProduct as CinemaProductServer;
use think\Request;

class Cinema
{
    /**
     * 影院详情
     * @param Request $request
     * @return \think\response\Json
     * $data 13/1/2020 下午3:41
     */
    public function getInfo(Request $request)
    {
        $cinemaId = $request->get('cinema_id');

        $service = new Manager((new \app\common\typeCode\manager\Cinema()));

        $info = $service->getInfoByGroupCode($cinemaId);

        if(empty($info)) return json(['code'=>0,'msg'=>'影院不存在']);

        $return = [
            'cinema_id' => $cinemaId,
            'pics' => explode(',',$info['pics']),
            'name' => $info['name'],
            'desc' => $info['desc'],
            'tel' => $info['tel'],
            'province' => $info['province'],
            'city' => $info['city'],
            'county' => $info['county'],
            'address' => $info['address'],
            'attr' => [],
        ];

        //获取影院属性值
        $attrs = (new CategoryObjHaveAttr((new \app\common\typeCode\cateObjHave\Cinema())->getObjType()))->getList($cinemaId);

        foreach ($attrs as $k => $v) {
            $return['attr'][$v['cate_name']] = $v['attr_value'];
        }

        //获取全部的影院的周边的选择
        $cinemaEarByTypeCode = (new CinemaNearby());
        $selectAroundList = (new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema())))->getList($cinemaEarByTypeCode,$cinemaId)->toArray();
        foreach ($selectAroundList as $rKey=>$rValue){
            $return['rim'][] = $rValue['cate_name'];
        }

        return json(['code' => 1, 'msg' => 'success', 'data' => $return]);

    }

    //获取影院的产品分类
    public function getProductCate(Request $request)
    {
        $cateModel = new CateModel();

        $productCateTypeCode = (new ProductCateTypeCode());


        $cinemaId = $request->get('cinema_id');

        $result = $cateModel->where(['type' => $productCateTypeCode->getCateType()])->field('id,name')
            ->whereIn('id', function ($query) use ($cinemaId) {
                //查询出拥有特定属性的云库ID
                $query->name('cinema_product')->field('cate_id')->where(['cinema_id' => $cinemaId])
                    ->group('cate_id');
            })->select();

        return json(['code' => 1, 'msg' => 'success', 'data' => $result]);
    }

    //根据分类获取产品
    public function getProduct(Request $request)
    {
        $get = $request->get();

        $rules = [
            'cinema_id' => 'require',
            'cate_id' => 'require',
        ];

        //查询该产品类别是不是影厅内的
        $productRule = (new ProductRule())->getByCateId($get['cate_id']);

        if(empty($productRule)) return json(['code'=>0,'msg'=>'类别不存在']);

        $productRule = $productRule->toArray();


        if($productRule['is_screen']==1){   //是影厅内的
            $result['is_screen'] = 'true';

            $product = (new CinemaProductServer($get['cinema_id']))->setShowType(false)->getEntityList(null,$get['cate_id'])->toArray();

            $ids = array_column($product,'screen_id');

            $screen = (new CinemaScreenServer())->getDataByCinemaId($ids);

            foreach ($screen as $screenKey=>$screenValue){
                $result['screen'][$screenKey]['name'] = $screenValue['name'];
                $result['screen'][$screenKey]['seat_sum'] = $screenValue['seat_sum'];
                $init = 0;
                foreach ($product as $productKey=>$productValue){
                    if($productValue['screen_id']==$screenValue['id']){
                        //查询该产品有没有其他的自定义字段
//                        (new CinemaProductServer())->getFieldList($specTypeCode);

                        $result['screen'][$screenKey]['product'][$init] = [
                            'id'=>$productValue['id'],
                            'entity_name'=>$productValue['entity_name'],
                            'price_month'=>$productValue['price_month'],
                            'price_year'=>$productValue['price_year'],
                            'price_everyday'=>$productValue['price_everyday'],
                        ];
                        $init++;
                    }
                }
            }
        }else{
            $result['is_screen'] = 'false';
            //查询该影院该分类下的产品
            $product = (new CinemaProductServer($get['cinema_id']))->setShowType(false)->getEntityList(null,$get['cate_id'])->toArray();
            foreach ($product as $value){
                $result['product'][] = [
                    'id'=>$value['id'],
                    'entity_name'=>$value['entity_name'],
                    'price_month'=>$value['price_month'],
                    'price_year'=>$value['price_year'],
                    'price_everyday'=>$value['price_everyday'],
                ];
            }
        }



        return json(['code' => 1, 'msg' => 'success', 'data' => $result]);

    }

    //获取影院筛选条件
    public function getCondition()
    {
        $category = new Category();
        $typeCode = new CinemaLevel();

        $data = $category->getList($typeCode);

        foreach ($data as $key => $value) {
            $data[$key]['children'] = $category->getAttrList($value['id']);
        }
        return json(['code' => 1, 'msg' => 'success', 'data' => $data]);

    }

    /**
     * 获取影院列表
     * @param Request $request
     * @return \think\response\Json
     * $data 13/1/2020 下午2:20
     */
    public function getList(Request $request)
    {
        $cityIds = $request->get('city_ids');  //市
        $cinemas = $request->get('cinemas');  //院线id
        $cinemaAttrIds = $request->get('cinema_attr_ids',[]);  //影院筛选条件ids

        //查询全部的影院
        $service = new \app\common\model\Manager();
        $handler = $service->alias('manager')->join('manager_info info','manager.info_id = info.id')
            ->field('*,info.id none_id,manager.id id,info.type info_type')->join('manager m2','m2.id = manager.group_code and m2.id = manager.id')
            ->where(['manager.type'=>4,'manager.delete_time'=>0,'manager.status'=>1]);

       if($cityIds) $handler->where('info.city_id',$cityIds);

       if($cinemas){
           $handler->where(function ($query) use ($cinemas){
               $query->where('tou_id',$cinemas)->whereOr('yuan_id',$cinemas);
           });
       }

       if(count($cinemaAttrIds) && !empty($cinemaAttrIds)){
           $handler->join('category_obj_have_attr attr','attr.object_id=manager.group_code and attr.type = 1')->whereIn('attr.attr_id', $cinemaAttrIds);;
       }


        $dataList = $handler->select()->toArray();

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

        //获取影院的周边选择
        $cinemaEarByTypeCode = (new CinemaNearby());
        //获取全部的影院的周边的选择
        $selectAroundList = (new CategoryObjHave((new \app\common\typeCode\cateObjHave\Cinema())))->getListAll($cinemaEarByTypeCode)->toArray();

        //组装级别   周边区域
        $result = ['code'=>1,'msg'=>'success','data'=>[]];
        foreach ($dataList as $key=>$value){
            $cinemaLevel = [];
            $rim = [];

            //级别
            if(isset($newLevelCheck[$value['id']])){
                foreach ($level as $levelKey=>$levelValue){
                    if(isset($newLevelCheck[$value['id']][$levelValue['id']])){
                        foreach ($levelValue['attr'] as $levelKey1=>$levelValue2){
                            if($newLevelCheck[$value['id']][$levelValue['id']] == $levelValue2['id']){
                                $cinemaLevel[$levelValue['name']]=$levelValue2['value'];
                            }
                        }
                    }
                }
            }
            //区域周边
            foreach ($selectAroundList as $selectAroundListKey=>$selectAroundListValue){
                if($selectAroundListValue['object_id']==$value['id']){
                    $rim[] = $selectAroundListValue['cate_name'];
                }
            }

            $result['data'][$key]['level'] = $cinemaLevel;
            $result['data'][$key]['rim'] = $rim;
            $result['data'][$key]['province'] = $value['province'];
            $result['data'][$key]['city'] = $value['city'];
            $result['data'][$key]['county'] = $value['county'];
            $result['data'][$key]['desc'] = $value['desc'];
            $result['data'][$key]['pic'] = explode(',',$value['pics'])[0];
            $result['data'][$key]['id'] = $value['id'];
            $result['data'][$key]['cinema_name'] = $value['name'];
        }

        return json($result);

    }

}
