<?php
declare (strict_types=1);

namespace app\bAdmin\controller;

use app\common\model\CategoryAttr;
use app\common\service\Category;
use app\common\typeCode\cate\CinemaLevel as TypeDesc;
use think\facade\Validate;
use think\facade\View;
use think\Request;

class CinemaCateLevel extends Base
{
    public function index()
    {
        try {

            $data = (new Category())->getList((new TypeDesc()));

            View::assign('cate', $data);

            return view();

        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }

    public function add()
    {
        return view();
    }

    public function save(Request $request)
    {
        $post = $request->post();

        $model = new CategoryAttr();
        $model->startTrans();
        try {
            $validate = Validate::rule([
                'name|分类名称' => 'require|max:60',
                'order_num|排序' => 'require|between:0,999',
                '__token__' => 'token',
            ]);

            $service = new Category();

            $typeDesc = new TypeDesc();

            if (!$validate->check($post)) throw new \Exception($validate->getError());

            $id = $service->insert($typeDesc, $post);

            if ($typeDesc->issetAttr()) {
                foreach ($post['attr_value'] as $key => $value) {
                    $service->insertAttr($id, [
                        'order_num' => $post['attr_order'][$key],
                        'value' => $value,
                    ]);
                }
            }

            $model->commit();

            return json(['code' => 1, 'msg' => 'success']);

        } catch (\Exception $e) {
            $model->rollback();
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->param('cate_id');

        $service = new Category();

        $typeDesc = new TypeDesc();

        if ($typeDesc->issetAttr()) {
            $attr = $service->getAttrList($id);
        } else {
            $attr = [];
        }

        View::assign('attr', $attr);
        View::assign('cate', $service->getOneById($id));

        return view();

    }

    public function update(Request $request)
    {
        $post = $request->post();

        $model = new CategoryAttr();

        $model->startTrans();
        try {
            $validate = Validate::rule([
                'id' => 'require',
                'name|分类名称' => 'require|max:60',
                'order_num|排序' => 'require|between:0,999',
                '__token__' => 'token',
            ]);

            if (!$validate->check($post)) throw new \Exception($validate->getError());

            $service = new Category();

            $typeDesc = new TypeDesc();

            $service->update($typeDesc, $post);

            if ($typeDesc->issetAttr()){
                $oldAttrIds = [];

                if (isset($post['old_attr_id'])){
                    foreach ($post['old_attr_id'] as $key => $value){
                        //修改老的数据
                        $service->updateAttr($value,[
                            'value' => $post['old_attr_value'][$key],
                            'order_num' => $post['old_attr_order'][$key],
                        ]);
                        $oldAttrIds[] = $value;
                    }
                }

                //删除没有的老的数据
                $service->deleteAttrByCateId($post['id'],$oldAttrIds);

                //新增新的数据
                if (isset($post['attr_value'])){
                    foreach ($post['attr_value'] as $key => $value){
                        $service->insertAttr($post['id'],[
                            'value' => $post['attr_value'][$key],
                            'order_num' => $post['attr_order'][$key],
                        ]);
                    }

                }
            }

            $model->commit();

            return json(['code' => 1, 'msg' => 'success']);

        } catch (\Exception $e) {

            $model->rollback();

            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $cate_id = $request->post('cate_id');

        (new Category())->delete((new TypeDesc()), $cate_id);

        return json(['code' => 1, 'msg' => 'success']);
    }
}