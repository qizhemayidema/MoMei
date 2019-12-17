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
use app\common\tool\Upload;
use app\common\typeCode\productField\Level;
use app\common\typeCode\productField\Spec;
use think\facade\Db;
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

//        $levelId = ($cateId && $request->param('level_id')) ? $request->param('level_id') : 0;

        $screenId = $request->param('screen_id') ?? 0;


        //获取所有分类
        $cate = (new Category())->getList((new \app\common\typeCode\cate\Product()));

        //获取所有级别
//        $level = $cateId ? (new ProductRule())->getFieldList((new Level()),$cateId) : [];

        //获取所有影厅
        $screen = (new CinemaScreen())->getList($this->user['group_code']);

        //获取entity下的数据
        $list = $service->setShowType(true)->getEntityList(15,$cateId,$screenId);

        View::assign(compact('list','cateId','levelId','screenId','cate','screen'));

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
            'price_discount|优惠价格' => 'require|float|max:10',
            'pic|封面图'           => 'require|max:255',
            'roll_pic|轮播图'      => 'require',
            'desc'          => 'require',
        ];

        $validate = Validate::rule($rules);


        $service = new CinemaProduct($this->user['group_code']);


        //获取规则
        $rule = (new ProductRule())->getByCateId($post['cate_id']);

        //数量
        $sum = $rule['max_sum'];

        $productRuleService = new ProductRule();

        $levelTypeCode = new Level();

        $specTypeCode = new Spec();

        Db::startTrans();
        try{
            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            if (count(explode(',',$post['roll_pic'])) > 8 ){
                throw new ValidateException('轮播图最多只能上传8张');
            }
            if (isset($post['spec'])) {
                foreach ($post['spec'] as $k => $v) {
                    $strLen = mb_strlen($v);
                    if ($strLen > 127 || $strLen == 0) {
                        $temp = $productRuleService->getField($k);
                        throw new ValidateException($temp['name'] . '不能为空且最大长度为127');
                    }
                }
            }

            //判断数量
            $count = $service->getSum($post['cate_id'],$post['level_id'],$post['screen_id']);

            if ($count + 1 > $sum) throw new ValidateException('该类型产品已达上限,无法继续操作');


            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($this->user['info_id']);

            //获取影厅名称
            $screenName = $post['screen_id'] ? (new CinemaScreen())->get($post['screen_id'])['name'] : '';

            //获取分类名称
            $cateName = (new Category())->get($post['cate_id'])['name'];


            //获取级别
            $level = $post['level_id'] ?  $productRuleService->getField($post['level_id']) : [];

            //获取自定义字段
            $specIds = isset($post['spec']) ? array_keys($post['spec']) : [];
            $spec = $productRuleService->getField($specIds);

            //组装数据
            $data = [
                'cinema_id' => $user['group_code'],
                'cate_id'   => $post['cate_id'],
                'screen_id' => $post['screen_id'],
                'cate_name' => $cateName,
                'pic'       => $post['pic'],
                'roll_pic'  => $post['roll_pic'],
                'cinema_name' => $cinemaInfo['name'],
                'screen_name' => $screenName,
                'entity_name'  => $post['entity_name'],
                'price_json' => $post['price_json'],
                'price_month' => $post['price_month'],
                'price_year' => $post['price_year'],
                'price_discount'    => $post['price_discount'],
                'desc'       => $post['desc'],
            ];


            $id = $service->insert($data);

            //插入实体类状态
            $service->insertEntityDayPriceStatus($id,$data['price_json']);

            //各种自定义字段
            if ($level){
                $levelArr = [
                    [
                        'product_field_id' => $level['id'],
                        'name'  => '级别',
                        'value' => $level['name']
                    ]
                ];
                $service->insertField($levelTypeCode,$levelArr,$id);
            }
            if ($spec){
                $specArr = [];
                foreach ($spec as $key => $value){
                    $specArr[] = [
                        'product_field_id' => $value['id'],
                        'name' => $value['name'],
                        'value' => $post['spec'][$value['id']],
                    ];
                }
                $service->insertField($specTypeCode,$specArr,$id);
            }

            Db::commit();
        }catch (ValidateException $e){
            Db::rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        } catch (\Exception $e){
            Db::rollback();
            return json(['code'=>0,'msg'=>$e->getMessage().$e->getLine().$e->getFile()]);
            return json(['code'=>0,'msg'=>'操作失误,请稍后再试']);
        }

        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $entityId = $request->param('id');

        $cinemaPService = new CinemaProduct($this->user['group_code']);

        $service = new PRoleService();

        $levelTypeCode = new Level();

        $specTypeCode = new Spec();

        //获取数据
        $data = $cinemaPService->getEntity($entityId);

        //获取影院影厅数据
        $screen = (new CinemaScreen())->getList($this->user['group_code']);

        //获取规则
        $rule = $service->getByCateId($data['cate_id']);

        //获取规则级别
        $level = $service->getFieldByRuleId($levelTypeCode,$rule['id']);

        //获取规则自定义字段
        $spec = $service->getFieldByRuleId($specTypeCode,$rule['id']);

        //获取产品级别
        $cinemaLevel = $cinemaPService->getFieldList($levelTypeCode,$entityId);

        //获取产品自定义字段
        $cinemaSpec = $cinemaPService->getFieldList($specTypeCode,$entityId);

        View::assign('rule', $rule);

        View::assign('level', $level);

        View::assign('screen',$screen);

        View::assign('data',$data);

        View::assign('spec',$spec);

        View::assign('cinema_level',$cinemaLevel);

        View::assign('cinema_spec',$cinemaSpec);

        return view();
    }

    public function update(Request $request)
    {
        $post = $request->post();

        $rules = [
            'id'            => 'require',
            'level_id|级别' => 'require',
            'screen_id|影厅' => 'require',
            'price_month|包月价格' => 'require|float|max:10',
            'price_year|包年价格'   => 'require|float|max:10',
            'price_json|每日价格'   => 'require',
            'entity_name|名称'       => 'require|max:30',
            'desc|介绍'       => 'require',
            'cate_id|分类' => 'require',
            'price_discount|优惠价格' => 'require|float|max:10',
            'pic|封面图'           => 'require|max:255',
            'roll_pic|轮播图'      => 'require',
            'desc'          => 'require',
//            '__token__'      => 'token'
        ];

        $productRuleService = new ProductRule();

        $levelTypeCode = new Level();

        $specTypeCode = new Spec();

        $validate = Validate::rule($rules);

        //获取规则
        $rule = (new ProductRule())->getByCateId($post['cate_id']);

        //数量
        $sum = $rule['max_sum'];

        Db::startTrans();

        $service = (new CinemaProduct($this->user['group_code']));

        try{
            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            $data = $service->getEntity($post['id']);
            if ($data['status'] == 1) throw new ValidateException('请将产品下架后再编辑');

            if (count(explode(',',$post['roll_pic'])) > 8 ){
                throw new ValidateException('轮播图最多只能上传8张');
            }

            if (isset($post['spec'])){
                foreach ($post['spec'] as $k => $v){
                    $strLen = mb_strlen($v);
                    if ($strLen > 127 || $strLen == 0){
                        $temp = $productRuleService->getField($k);
                        throw new ValidateException($temp['name'].'不能为空且最大长度为127');
                    }
                }
            }

            //判断数量
            $count = $service->getSum($post['cate_id'],$post['level_id'],$post['screen_id']);

            if ($count + 1 > $sum) throw new ValidateException('该类型产品已达上限,无法继续操作');

            //获取影院相关信息
            $cinemaInfo = (new Manager())->getInfo($this->user['info_id']);

            //获取影厅名称
            $screenName = $post['screen_id'] ? (new CinemaScreen())->get($post['screen_id'])['name'] : '';

            //获取分类名称
            $cateName = (new Category())->get($post['cate_id'])['name'];


            //获取级别
            $level = $post['level_id'] ?  $productRuleService->getField($post['level_id']) : [];

            //获取自定义字段
            $specIds = isset($post['spec']) ? array_keys($post['spec']) : [];
            $spec = $productRuleService->getField($specIds);


            //组装数据
            $data = [
                'screen_id' => $post['screen_id'],
                'cinema_name' => $cinemaInfo['name'],
                'entity_name'      => $post['entity_name'],
                'desc'      => $post['desc'],
                'screen_name' => $screenName,
                'cate_name' => $cateName,
                'price_json'        => $post['price_json' ],
                'price_month'       => $post['price_month'],
                'price_year'        => $post['price_year' ],
                'price_discount'    => $post['price_discount'],
                'pic'       => $post['pic'],
                'roll_pic'  => $post['roll_pic'],
                'status'    => 2,
            ];

            $service = (new CinemaProduct($this->user['group_code']));

            $service->update($post['id'],$data);

            //各种自定义字段
            $service->deleteField($levelTypeCode,$post['id']);
            if ($level){
                $levelArr = [
                    [
                        'product_field_id' => $level['id'],
                        'name'  => '级别',
                        'value' => $level['name']
                    ]
                ];
                $service->insertField($levelTypeCode,$levelArr,$post['id']);
            }
            $service->deleteField($specTypeCode,$post['id']);

            if ($spec){
                $specArr = [];
                foreach ($spec as $key => $value){
                    $specArr[] = [
                        'product_field_id' => $value['id'],
                        'name' => $value['name'],
                        'value' => $post['spec'][$value['id']],
                    ];
                }
                $service->insertField($specTypeCode,$specArr,$post['id']);
            }


            Db::commit();
        }catch (ValidateException $e){
            Db::rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        } catch (\Exception $e){
            Db::rollback();
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

        //获取级别
        $level = $service->getFieldByRuleId((new Level()),$rule['id']);

        //获取自定义字段
        $spec = $service->getFieldByRuleId((new Spec()),$rule['id']);

        //获取影院影厅数据
        $screen = (new CinemaScreen())->getList($this->user['group_code']);

        View::assign('rule', $rule);

        View::assign('cate_id', $cateId);

        View::assign('level', $level);

        View::assign('screen',$screen);

        View::assign('spec',$spec);

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

    public function uploadPic()
    {
        return json((new Upload())->uploadOnePic('product/'));
    }
}