<?php
declare (strict_types=1);

namespace app\bAdmin\controller;

use app\common\service\Category;
use app\common\service\ProductRule as Service;
use app\common\typeCode\cate\Product;
use think\exception\ValidateException;
use think\Request;
use think\facade\View;
use think\Validate;

class ProductRule
{
    public function index()
    {
        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $rules = [
            'cate_id' => 'require',
            'sum|产品数量' => 'require|integer',
            'select_max_sum|最多选择数量' => 'require|integer',
            'is_screen|是否影厅' => 'require',
            'is_level|是否开启级别' => 'require',
        ];

        $service = new Service();

        $validate = new Validate();

        $validate->rule($rules);

        $model = (new \app\common\model\Product());
        $model->startTrans();

        try {
            if (!$validate->check($post)) throw new ValidateException($validate->getError());

            $post['cate_name'] = (new Category())->get($post['cate_id'])['name'];

            $id = $service->existsCateId($post['cate_id']);

            //product的id
            if (!$id){
                $id = $service->insert($post);
            }else{
                $service->update($post,$id);
            }

            //判断产品级别
            if (isset($post['old_level_id'])) {
                $oldData = [];
                $ids = [];
                foreach ($post['old_level_id'] as $key => $value) {
                    $oldData[] = [
                        'id' => $value,
                        'level_name' => $post['old_level_name'][$key],
                    ];
                    $ids[] = $value;
                }

                $levelExceptIds = $service->getLevelExceptIds($ids, $id);

                $service->deleteLevel($levelExceptIds, $id);

                $service->updateLevel($oldData, $id);
            }

            if ($post['is_level'] == 1 && isset($post['level_name'])){
                $service->insertLevel($post, $id);
            }

            $model->commit();
        } catch (ValidateException|\Exception $e) {
            $model->rollback();
            return json(['code'=>0,'msg'=>$e->getMessage() . $e->getLine()]);
        }
        return json(['code'=>1,'msg'=>'success']);
    }

    public function edit(Request $request)
    {
        $id = $request->post('id');

        $service = new Service();

        $data = $service->getByCateId($id);

        $level = $service->getLevelByProductId($data['id']);

        View::assign('data', $data);

        View::assign('id', $id);

        View::assign('level', $level);

        return view();

    }

    public function getCateList()
    {
        $list = (new Category())->getList((new Product()));

        return json(['code' => 1, 'data' => $list]);
    }


}
