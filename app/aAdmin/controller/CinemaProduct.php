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
    public function index()
    {
        $info = (new Session())->getData();

        $managerService = new ManagerService(new CinemaTypeDesc());

        $field = '';
        if($info['type']==2){  //院线
            $field = 'yuan_id';
        }elseif ($info['type']==3){ //影投
            $field = 'tou_id';
        }

        $cinemaData = $managerService->setWhere('info',$field,$info['group_code'])->getList(); //属于该影投/院线的影院

        $cinemaIds = array_column($cinemaData,'group_code');   //这里是所属的全部影院的group_code

        $productResult = (new CinemaProductService())->getList(15);  //该资源方下全部影院的全部产品

        View::assign('data',$productResult);

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