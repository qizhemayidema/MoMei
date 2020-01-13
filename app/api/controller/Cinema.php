<?php
declare (strict_types=1);

namespace app\api\controller;

use app\common\model\CinemaProduct as CinemaProductModel;
use app\common\model\Category as CateModel;
use app\common\service\Category;
use app\common\service\CategoryObjHave;
use app\common\service\ProductRule;
use app\common\typeCode\cate\CinemaNearby;
use app\common\typeCode\cate\Product as ProductCateTypeCode;
use app\common\service\CategoryObjHaveAttr;
use app\common\service\Manager;
use app\common\typeCode\cate\CinemaLevel;
use think\Request;

class Cinema
{
    public function getInfo(Request $request)
    {
        $cinemaId = $request->get('cinema_id');

        $service = new Manager((new \app\common\typeCode\manager\Cinema()));

        $info = $service->getInfoByGroupCode($cinemaId);

        $return = [
            'cinema_id' => $cinemaId,
            'pics' => $info['pics'],
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
            $return['attr'][] = $v['attr_value'];
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

        //查询分类规则
        $productRule = (new ProductRule())->get($get['cate_id']);

        if (!$productRule) return json(['code' => 1, 'msg' => 'success', 'data' => []]);

//        if ($productRule[''])
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

    public function getList()
    {
        //查询全部的影院
        $service = new Manager((new \app\common\typeCode\manager\Cinema()));
        $dataList = $service->getInfoList()->toArray();

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
//        $zhou = $cateService->getList($cinemaEarByTypeCode);
//        $newZhou = array_column($zhou,NULL,'id');
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
//                    if(isset($newZhou[$selectAroundListValue['cate_id']])){
//                        $rim[] = $newZhou[$selectAroundListValue['cate_id']]['name'];
//                    }
                }
            }

            $result['data'][$key]['level'] = $cinemaLevel;
            $result['data'][$key]['rim'] = $rim;
            $result['data'][$key]['province'] = $value['province'];
            $result['data'][$key]['city'] = $value['city'];
            $result['data'][$key]['county'] = $value['county'];
            $result['data'][$key]['desc'] = $value['desc'];
            $result['data'][$key]['desc'] = $value['desc'];
            $result['data'][$key]['id'] = $value['id'];
        }

        return json($result);

    }
}
