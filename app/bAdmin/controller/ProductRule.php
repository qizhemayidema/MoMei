<?php
declare (strict_types=1);

namespace app\bAdmin\controller;

use app\common\service\Category;
use app\common\service\ProductRule as Service;
use app\common\typeCode\cate\Product;
use app\common\typeCode\productField\Level;
use app\common\typeCode\productField\Spec;
use app\common\typeCode\productField\Text;
use think\exception\ValidateException;
use think\Request;
use think\facade\View;
use think\Validate;

class ProductRule extends Base
{
    public function index()
    {
        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $rules = [
            'is_open' => 'require',
            'cate_id' => 'require',
            'sum_unit|数量单位' => 'require|max:12',
            'type|产品数量类型' => 'require|integer',
            'select_max_sum|最多选择数量' => 'require|integer',
            'is_screen|是否影厅' => 'require',
//            'is_level|是否开启级别' => 'require',
        ];

        $service = new Service();

        $validate = new Validate();

        $validate->rule($rules);

        $model = (new \app\common\model\ProductRule());

        $model->startTrans();

        $levelTypeCode = new Level();

        $specTypeCode = new Spec();

        $textTypeCode = new Text();

//        return json(['code'=>0,'msg'=>$post]);
        try {

            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            $post['cate_name'] = (new Category())->get($post['cate_id'])['name'];

            $id = $service->existsCateId($post['cate_id']);

            //如果什么都没有 给0
            if (!isset($post['old_level_name']) && !isset($post['level_name'])) $post['is_level'] = 0;
            if (!isset($post['old_spec_name']) && !isset($post['spec_name'])) $post['is_spec'] = 0;
            if (!isset($post['old_text_name']) && !isset($post['text_name'])) $post['is_text'] = 0;

            $service->update($post,$id);

            //判断规则的级别
            if (isset($post['old_level_id'])) {
                $oldData = [];
                $ids = [];
                foreach ($post['old_level_id'] as $key => $value) {
                    $oldData[] = [
                        'id' => $value,
                        'name' => $post['old_level_name'][$key],
                    ];
                    $ids[] = $value;
                }

                $exceptIds = $service->getFieldExceptIds($levelTypeCode,$ids, $id);

                $service->deleteField($exceptIds, $id);

                $service->updateField($oldData, $id);
            }else{
                $service->deleteFieldAll($levelTypeCode, $id);
            }

            //判断规则的规格
            if (isset($post['old_spec_id'])) {
                $oldData = [];
                $ids = [];
                foreach ($post['old_spec_id'] as $key => $value) {
                    $oldData[] = [
                        'id' => $value,
                        'name' => $post['old_spec_name'][$key],
                    ];
                    $ids[] = $value;
                }

                $exceptIds = $service->getFieldExceptIds($specTypeCode,$ids, $id);

                $service->deleteField($exceptIds, $id);

                $service->updateField($oldData, $id);
            }else{
                $service->deleteFieldAll($specTypeCode, $id);
            }
            //判断规则的内容名称
            if (isset($post['old_text_id'])) {
                $oldData = [];
                $ids = [];
                foreach ($post['old_text_id'] as $key => $value) {
                    $oldData[] = [
                        'id' => $value,
                        'name' => $post['old_text_name'][$key],
                    ];
                    $ids[] = $value;
                }

                $exceptIds = $service->getFieldExceptIds($textTypeCode,$ids, $id);

                $service->deleteField($exceptIds, $id);

                $service->updateField($oldData, $id);
            }else{
                $service->deleteFieldAll($textTypeCode, $id);
            }

            if (isset($post['level_name'])){

                $temp = $post;

                $temp['name'] = $post['level_name'];

                $service->insertField($levelTypeCode,$temp, $id);

            }

            if (isset($post['spec_name'])){

                $temp = $post;

                $temp['name'] = $post['spec_name'];

                $service->insertField($specTypeCode,$temp, $id);

            }

            if (isset($post['text_name'])){

                $temp = $post;

                $temp['name'] = $post['text_name'];

                $service->insertField($textTypeCode,$temp, $id);

            }

            $model->commit();
        } catch (ValidateException|\Exception $e) {
            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage().$e->getFile() . $e->getLine()]);
        }
        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $id = $request->post('id');

        $service = new Service();

        $data = $service->getByCateId($id);

        $level = $service->getFieldByRuleId((new Level()),$data['id']);


        $spec = $service->getFieldByRuleId((new Spec()),$data['id']);

        $text = $service->getFieldByRuleId((new Text()),$data['id']);

        View::assign(compact('data','id','level','spec','text'));

        return view();

    }

    public function getCateList()
    {
        $list = (new Category())->getList((new Product()));

        return json(['code' => 1, 'data' => $list]);
    }


}
