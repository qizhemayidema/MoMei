<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\BaseController;
use app\common\service\Category;
use app\common\service\CinemaProduct;
use app\common\service\CinemaScreen;
use app\common\service\Manager;
use app\common\service\ProductRule as PRoleService;
use app\common\service\ProductRule;
use think\facade\Validate;
use think\facade\View;
use app\common\tool\Session;
use think\Request;

class Product extends Base
{
    public function index()
    {
        $user = (new Session())->getData();

        $list = (new CinemaProduct())->getList($user['group_code'],15);

        View::assign('list',$list);

        return view();

    }

    public function add()
    {
        return view('add_index');
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $user = (new Session())->getData();

        $rules = [
            'cate_id'   => 'require',
            'level_id|级别' => 'require',
            'screen_id|影厅' => 'require',
            'sum|数量'        => 'require',
            'name|名称'       => 'require|max:30',
            'desc|介绍'       => 'require',
//            '__token__'      => 'token'
        ];


        $validate = Validate::rule($rules);

        try{
            if (!$validate->check($post)) throw new \Exception($validate->getError());

            $productRoleService = (new ProductRule());

            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($user['info_id']);

            //获取影厅名称
            $screenName = $post['screen_id'] ? (new CinemaScreen())->get($post['screen_id'])['name'] : '';

            //获取分类名称
            $cateName = (new Category())->get($post['cate_id'])['name'];

            //级别名称
            $levelName = $post['level_id'] ? $productRoleService->getLevel($post['level_id'])['level_name'] : '';

            //数量类型
            $type = $post['sum'] > 1 ? 2 : 1;

            //获取规则
            $rule = $productRoleService->getByCateId($post['cate_id']);

            //组装数据
            $data = [
                'cinema_id' => $user['group_code'],
                'cate_id'   => $post['cate_id'],
                'screen_id' => $post['screen_id'],
                'level_id'  => $post['level_id'],
                'cinema_name' => $cinemaInfo['name'],
                'level_name' => $levelName,
                'name'      => $post['name'],
                'desc'      => $post['desc'],
                'screen_name' => $screenName,
                'cinema_cate_name' => $cateName,
                'type'      => $type,
                'select_max_sum' => $rule['select_max_sum'],
                'sum'       => $post['sum'],
                'status'    => 2,
            ];

            (new CinemaProduct())->insert($data);


        }catch (\Exception $e){
            return json(['code'=>0,'msg'=>$e->getMessage().$e->getFile().$e->getLine()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function getFormHtml(Request $request)
    {
        $cateId = $request->param('id');

        $user = (new Session())->getData();

        $service = new PRoleService();

        //获取规则
        $rule = $service->getByCateId($cateId);

        //获取规则级别
        $level = $service->getLevelByProductId($rule['id']);

        //获取影院影厅数据
        $screen = (new CinemaScreen())->getList($user['group_code']);

        View::assign('rule', $rule);

        View::assign('cate_id', $cateId);

        View::assign('level', $level);

        View::assign('screen',$screen);

        return view('add');
    }



    public function entity(Request $request)
    {
        $id = $request->param('id');

        View::assign('id',$id);

        return view('entity_index');
    }

    public function getEntityHtml(Request $request)
    {
        $id = $request->post('id');

        $groupCode = (new Session())->getData()['group_code'];

        $service = new CinemaProduct($groupCode);

        $entity = $service->getEntity($id);

        View::assign('data',$entity);

        return \view('entity');

    }

    public function getEntityList(Request $request)
    {
        $id = $request->param('id');

        $user = (new Session())->getData();

        $groupCode = $user['group_code'];

        $service = new CinemaProduct($groupCode);


        //获取entity下的数据
        $data = $service->setEntityShowType(true)->getEntityList($id);

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

    public function getCateList()
    {
        $list = (new Category())->getList((new \app\common\typeCode\cate\Product()));

        return json(['code' => 1, 'data' => $list]);

    }

    public function changeStatus(Request $request)
    {
        $status = $request->post('status');
        $id = $request->post('id');

        (new CinemaProduct())->changeStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);
    }
}