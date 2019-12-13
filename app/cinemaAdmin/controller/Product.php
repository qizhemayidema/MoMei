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

    public function index(Request $request)
    {
        $service = new CinemaProduct($this->user['group_code']);

        $cateId = $request->param('cate_id') ?? 0;

        $levelId = ($cateId && $request->param('level_id')) ? $request->param('level_id') : 0;

        $screenId = $request->param('screen_id') ?? 0;


        //获取所有分类
        $cate = (new Category())->getList((new \app\common\typeCode\cate\Product()));

        //获取所有级别
        $level = $cateId ? (new ProductRule())->getLevelList($cateId) : [];

        //获取所有影厅
        $screen = (new CinemaScreen())->getList($this->user['group_code']);

        //获取entity下的数据
        $list = $service->setShowType(true)->getEntityList(15,$cateId,$levelId,$screenId);

        View::assign(compact('list','cateId','levelId','screenId','cate','level','screen'));

        return view();

    }

    public function add()
    {
        $list = (new Category())->getList((new \app\common\typeCode\cate\Product()));

        View::assign('cate',$list);
        return view('add_index');
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $user = (new Session())->getData();

        $rules = [
            'cate_id|分类' => 'require',
            'level_id|级别' => 'require',
            'screen_id|影厅' => 'require',
            'price_month|包月价格' => 'require|float|max:10',
            'price_year|包年价格'   => 'require|float|max:10',
            'price_json|每日价格'   => 'require',
            'desc'          => 'require',
        ];

        $validate = Validate::rule($rules);

        $model = new CinemaProductEntity();

        $service = new CinemaProduct($this->user['group_code']);


        //获取规则
        $rule = (new ProductRule())->getByCateId($post['cate_id']);

        //数量
        $sum = $rule['max_sum'];

        $model->startTrans();
        try{
            if (!$validate->check($post)) throw new ValidateException($validate->getError());


            //判断数量
            $count = $service->getSum($post['cate_id'],$post['level_id'],$post['screen_id']);
            if ($count + 1 > $sum) throw new ValidateException('无法修改,组合数量达到上限');


            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($this->user['info_id']);



            //获取影厅名称
            $screenName = $post['screen_id'] ? (new CinemaScreen())->get($post['screen_id'])['name'] : '';

            //获取分类名称
            $cateName = (new Category())->get($post['cate_id'])['name'];


            //级别名称
            $levelName = $post['level_id'] ?  (new ProductRule())->getLevel($post['level_id'])['level_name'] : '';

            //组装数据
            $data = [
                'cinema_id' => $user['group_code'],
                'cate_id'   => $post['cate_id'],
                'screen_id' => $post['screen_id'],
                'level_id'  => $post['level_id'],
                'cate_name' => $cateName,
                'cinema_name' => $cinemaInfo['name'],
                'screen_name' => $screenName,
                'level_name' => $levelName,
                'entity_name'  => $post['entity_name'],
                'price_json' => $post['price_json'],
                'price_month' => $post['price_month'],
                'price_year' => $post['price_year'],
                'desc'       => $post['desc'],
            ];


            $id = $service->insert($data);

            //插入实体类状态
            $service->insertEntityDayPriceStatus($id,$data['price_json']);

            $model->commit();
        }catch (ValidateException $e){
            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        } catch (\Exception $e){
            $model->rollback();

            return json(['code'=>0,'msg'=>'操作失误,请稍后再试']);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $entityId = $request->param('id');

        //获取数据
        $data = (new CinemaProduct($this->user['group_code']))->getEntity($entityId);

//        dump($data->toArray());die; 包月 包年 每日
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
            'price_month|包月价格' => 'require|float',
            'price_year|包年价格'   => 'require|float',
            'price_json|每日价格'   => 'require',
            'entity_name|名称'       => 'require|max:30',
            'desc|介绍'       => 'require',
//            '__token__'      => 'token'
        ];


        $validate = Validate::rule($rules);

        $model = new \app\common\model\CinemaProduct();
        $service = (new CinemaProduct($this->user['group_code']));

        $model->startTrans();
        try{
            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            $data = $service->getEntity($post['id']);
            if ($data['status'] == 1) throw new ValidateException('请将产品下架后再编辑');

            $productRoleService = (new ProductRule());

            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($this->user['info_id']);

            //获取影厅名称
            $screenName = $post['screen_id'] ? (new CinemaScreen())->get($post['screen_id'])['name'] : '';

            //获取分类名称
            $cateName = (new Category())->get($post['cate_id'])['name'];


            //级别名称
            $levelName = $post['level_id'] ? $productRoleService->getLevel($post['level_id'])['level_name'] : '';

            //获取规则
            $rule = $productRoleService->getByCateId($data['cate_id']);

            //数量
            $sum = $rule['max_sum'];

            //判断数量
            $count = $service->getSum($data['cate_id'],$post['level_id'],$post['screen_id'],[$post['id']]);
            if ($count + 1 > $sum) throw new ValidateException('无法修改,组合数量达到上限');


            //组装数据
            $data = [
//                'cinema_id' => $this->user['group_code'],
                'screen_id' => $post['screen_id'],
                'level_id'  => $post['level_id'],
                'cinema_name' => $cinemaInfo['name'],
                'level_name' => $levelName,
                'entity_name'      => $post['entity_name'],
                'desc'      => $post['desc'],
                'screen_name' => $screenName,
                'cate_name' => $cateName,
                'price_json'        => $post['price_json' ],
                'price_month'       => $post['price_month'],
                'price_year'        => $post['price_year' ],
//                'type'      => $type,
//                'select_max_sum' => $rule['select_max_sum'],
//                'sum'       => $sum,
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
            return json(['code'=>0,'msg'=>$e->getMessage()]);
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


    public function getCateList()
    {
        $list = (new Category())->getList((new \app\common\typeCode\cate\Product()));

        return json(['code' => 1, 'data' => $list]);

    }
}