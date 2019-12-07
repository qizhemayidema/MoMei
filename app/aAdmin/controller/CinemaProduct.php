<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 15:15
 */
declare (strict_types = 1);
namespace app\aAdmin\controller;

use app\common\service\ProductRule;
use app\common\tool\Session;
use app\common\service\CinemaProduct as CinemaProductService;
use app\common\typeCode\manager\Cinema as CinemaTypeDesc;
use app\Request;
use think\facade\View;
use app\common\service\Manager as ManagerService;

class CinemaProduct extends Base
{
    public function index(Request $request)
    {
        $cinemaId = $request->param('cinema_id');   //接收影院的id(group_code)

        $productResult = (new CinemaProductService($cinemaId))->setShowType(true)->getList(15);  //该资源方下全部影院的全部产品

        View::assign('group_code',$cinemaId);

        View::assign('data',$productResult);

        return view();
    }

    public function info(Request $request)
    {
        $id = $request->param('id');

        $groupCode = $request->param('group_code');

        View::assign('group_code',$groupCode);

        View::assign('id',$id);

        return view();
    }

    public function getEntityHtml(Request $request)
    {
        $id = $request->post('id');

        $productId = $request->post('product_id');

        $groupCode = $request->post('group_code');

        $service = new CinemaProductService($groupCode);

        $entity = $service->getEntity($id);

//        dump($entity);die;

        View::assign('data',$entity);

        View::assign('product_id',$productId);

        return \view('entity');

    }

    public function getEntityList(Request $request)
    {
        $id = $request->param('id');

        $groupCode = $request->post('group_code');

        $service = new CinemaProductService($groupCode);

        //获取entity下的数据
        $data = $service->setShowType(true)->getEntityList($id);

        //获取数量最大值
        $product = $service->get($id);

        $maxListNum = $product['sum'];

        $k = 0;

        $list = [];

        foreach ($data as $key => $value){
            $list[] = [
                'id'    => $value['id'],
                'entity_name' => $value['entity_name'],
                'price_json' => $value['price_json'],
                'price_month' => $value['price_month'],
                'price_year'  => $value['price_year'],
                'sort'        => $value['sort'],
            ];
            $k ++ ;
        }

        $length = $maxListNum - $k;

        for($i = 0;$i < $length;$i ++){
            $list[] = [
                'id'    => 0,
                'entity_name' => '暂未添加',
                'price_json'  => '',
                'price_month' => '',
                'price_year'  => '',
                'sort'        => '',
            ];
        }

        return json(['code'=>1,'data'=>$list]);
    }
}