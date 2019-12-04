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
use app\common\service\AUser as AUserService;
use app\common\service\CinemaProduct as CinemaProductService;
use app\Request;
use think\facade\View;

class CinemaProduct extends Base
{
    public function index()
    {
        $info = (new Session())->getData();

        $cinemaData = (new AUserService())->getAssociatedCinemaList($info['type'],$info['group_code']); //属于该影投/院线的影院

//        $productResult = (new CinemaProductService())->getProductList(array_column($cinemaData,'id'),1,15);  //该资源方下全部影院的全部产品

//        View::assign('data',$productResult);

        return view();
    }

    public function getDetail(Request $request)
    {
        $productId = $request->param('product_id');

        //查询此产品id去查询实体产品 价格等
        $productEntityResult = 1;

        return view();
    }
}