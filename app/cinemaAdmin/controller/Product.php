<?php
declare (strict_types = 1);

namespace app\cinemaAdmin\controller;

use app\BaseController;
use app\common\model\CinemaProductEntity;
use app\common\service\Category;
use app\common\service\CinemaProduct;
use app\common\service\CinemaScreen;
use app\common\service\Manager;
use app\common\service\ProductRule as PRoleService;
use app\common\service\ProductRule;
use think\exception\ValidateException;
use think\facade\Validate;
use think\facade\View;
use app\common\tool\Session;
use think\Request;

class Product extends Base
{
    private $user = null;

    public function initialize()
    {
        $this->user = (new Session())->getData();
    }

    public function index()
    {
        $list = (new CinemaProduct($this->user['group_code']))->setShowType(true)->getList(15);

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

        $rules = [
            'cate_id'   => 'require',
            'level_id|级别' => 'require',
            'screen_id|影厅' => 'require',
//            'sum|数量'        => 'require',
            'name|名称'       => 'require|max:30',
            'desc|介绍'       => 'require',
//            '__token__'      => 'token'
        ];


        $validate = Validate::rule($rules);

        try{
            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            $productRoleService = (new ProductRule());

            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($this->user['info_id']);

            //获取影厅名称
            $screenName = $post['screen_id'] ? (new CinemaScreen())->get($post['screen_id'])['name'] : '';

            //获取分类名称
            $cateName = (new Category())->get($post['cate_id'])['name'];

            //获取规则
            $rule = $productRoleService->getByCateId($post['cate_id']);

            //级别名称
            $levelName = $post['level_id'] ? $productRoleService->getLevel($post['level_id'])['level_name'] : '';

            //数量类型
            $type = $rule['type'];

            //数量
            $sum = $rule['max_sum'];



            //组装数据
            $data = [
                'cinema_id' => $this->user['group_code'],
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
                'sum'       => $sum,
                'status'    => 2,
            ];

            (new CinemaProduct($this->user['group_code']))->insert($data);


        }catch (ValidateException $e){
            return json(['code'=>0,'msg'=>$e->getMessage().$e->getFile().$e->getLine()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $productId = $request->param('id');

        $data = (new CinemaProduct($this->user['group_code']))->get($productId);

        $service = new PRoleService();

        //获取规则
        $rule = $service->getByCateId($data['cate_id']);

        //获取规则级别
        $level = $service->getLevelByProductId($rule['id']);

        //获取影院影厅数据
        $screen = (new CinemaScreen())->getList($this->user['group_code']);

        View::assign('rule', $rule);

        View::assign('level', $level);

        View::assign('screen',$screen);

        View::assign('data',$data);

        return view();
    }

    public function update(Request $request)
    {
        $post = $request->post();

        $rules = [
            'id'            => 'require',
            'level_id|级别' => 'require',
            'screen_id|影厅' => 'require',
//            'sum|数量'        => 'require',
            'name|名称'       => 'require|max:30',
            'desc|介绍'       => 'require',
//            '__token__'      => 'token'
        ];


        $validate = Validate::rule($rules);

        $model = new \app\common\model\CinemaProduct();

        $model->startTrans();
        try{
            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            $data = (new CinemaProduct($this->user['group_code']))->get($post['id']);
            if ($data['status'] == 1) throw new ValidateException('请将产品下架后再编辑');

            $productRoleService = (new ProductRule());

            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($this->user['info_id']);

            //获取影厅名称
            $screenName = $post['screen_id'] ? (new CinemaScreen())->get($post['screen_id'])['name'] : '';

            //获取分类名称
            $cateName = (new Category())->get($post['cate_id'])['name'];

            //获取规则
            $rule = $productRoleService->getByCateId($post['cate_id']);

            //级别名称
            $levelName = $post['level_id'] ? $productRoleService->getLevel($post['level_id'])['level_name'] : '';

            //数量类型
            $type = $rule['type'];

            //数量
            $sum = $rule['max_sum'];

            //组装数据
            $data = [
                'cinema_id' => $this->user['group_code'],
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
                'sum'       => $sum,
                'status'    => 2,
            ];

            $service = (new CinemaProduct($this->user['group_code']));

            //修改需要同步到实体类
            $service->update($post['id'],$data);

            $model->commit();
        }catch (ValidateException $e){
            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        } catch (\Exception $e){
            $model->rollback();
            return json(['code'=>0,$e->getMessage()]);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function delete(Request $request)
    {
        $id = $request->post('id');

        (new CinemaProduct($this->user['group_code']))->delete($id);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function getFormHtml(Request $request)
    {
        $cateId = $request->param('id');

        $service = new PRoleService();

        //获取规则
        $rule = $service->getByCateId($cateId);

        //获取规则级别
        $level = $service->getLevelByProductId($rule['id']);

        //获取影院影厅数据
        $screen = (new CinemaScreen())->getList($this->user['group_code']);

        View::assign('rule', $rule);

        View::assign('cate_id', $cateId);

        View::assign('level', $level);

        View::assign('screen',$screen);

        return view('add');
    }

    public function changeStatus(Request $request)
    {
        $status = $request->post('status');

        $id = $request->post('id');

        (new CinemaProduct($this->user['group_code']))->changeStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);
    }


    public function entity(Request $request)
    {
        $id = $request->param('id');

        View::assign('id',$id);

        return view('entity_index');
    }

    public function entitySave(Request $request)
    {
        $post = $request->post();

        $user = (new Session())->getData();

        $rules = [
            'id'   => 'require',
            'product_id' => 'require',
            'entity_name|名称' => 'require|max:64',
            'price_month|包月价格' => 'require',
            'price_year|名称'     => 'require',
            'price_day|日价格'       => 'require',
        ];

        $validate = Validate::rule($rules);

        $model = new CinemaProductEntity();

        $model->startTrans();
        try{
            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            $service = new CinemaProduct($this->user['group_code']);

            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($this->user['info_id']);

            //获取产品相关信息
            $productInfo = $service->get($post['product_id']);

//            if ($productInfo['status'] == 1) throw new ValidateException('请将产品下架后再编辑');

            if ($user['group_code'] != $productInfo['cinema_id']){
                throw new ValidateException('你要干嘛');
            }
            //组装数据
            $data = [
                'cinema_id' => $user['group_code'],
                'cate_id'   => $productInfo['cate_id'],
                'product_id' => $productInfo['id'],
                'screen_id' => $productInfo['screen_id'],
                'level_id'  => $productInfo['level_id'],
                'cate_name' => $productInfo['cinema_cate_name'],
                'cinema_name' => $cinemaInfo['name'],
                'screen_name' => $productInfo['screen_name'],
                'level_name' => $productInfo['level_name'],
                'product_name' => $productInfo['name'],
                'entity_name'  => $post['entity_name'],
                'sort'      => $post['sort'],
                'price_json' => $post['price_day'],
                'price_month' => $post['price_month'],
                'price_year' => $post['price_year'],
            ];

            if (!$post['id'] || $post['id'] == 0){   //修改

                $id = $service->insertEntity($data);

            }else{
                //获取实体状态
                $id = $post['id'];
                $entity = $service->getEntity($post['id']);
                if ($entity['status'] == 1) throw new ValidateException('请将产品实体下架后再编辑');

                $service->updateEntity($post['id'],$data);
            }

            //插入实体类状态
            $service->insertEntityDayPriceStatus($id,$data['price_json']);

            $model->commit();
        }catch (ValidateException $e){
            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        } catch (\Exception $e){
            $model->rollback();
            return json(['code'=>0,'操作失误,请稍后再试']);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function entityDelete(Request $request)
    {
        $entityId = $request->post('id');

        (new CinemaProduct($this->user['group_code']))->deleteEntity($entityId);

        return json(['code'=>1,'msg'=>'success']);
    }

    public function getEntityHtml(Request $request)
    {
        $id = $request->post('id');

        $productId = $request->post('product_id');

        $service = new CinemaProduct($this->user['group_code']);

        $entity = $service->getEntity($id);

//        dump($entity);die;

        View::assign('data',$entity);

        View::assign('product_id',$productId);

        return \view('entity');

    }

    public function getEntityList(Request $request)
    {
        $id = $request->param('id');

        $service = new CinemaProduct($this->user['group_code']);

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
                'status'      => $value['status'],

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
                'status'      => 0,
            ];
        }

        return json(['code'=>1,'data'=>$list]);
    }

    public function getCateList()
    {
        $list = (new Category())->getList((new \app\common\typeCode\cate\Product()));

        return json(['code' => 1, 'data' => $list]);

    }

    public function changeEntityStatus(Request $request)
    {
        $status = $request->post('status');
        $id = $request->post('id');

        (new CinemaProduct($this->user['group_code']))->changeEntityStatus($id,$status);

        return json(['code'=>1,'msg'=>'success']);
    }

}