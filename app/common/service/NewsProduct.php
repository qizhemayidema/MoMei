<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/29
 * Time: 9:31
 */

namespace app\common\service;


use app\Request;

class NewsProduct
{
    /**
     * 查询新闻
     * @param bool $cateId      int  类别id
     * @param bool $del         true  未删除的    false 删除的
     * @param bool $page        false 不分页     有值 分页数量
     * $data 2019/11/29 9:40
     */
    public function getProductList($cateId = false,$del = true,$page = false)
    {
        $newsProductModel = new \app\common\model\NewsProduct();

        if($cateId) $newsProductModel = $newsProductModel->where(['cate_id'=>$cateId]);

        $newsProductModel = $del ? $newsProductModel->where(['delete_time'=>0])->order('sort desc') : $newsProductModel->where(['delete_time','>',0])->order('sort desc');

        return $page ? $newsProductModel->paginate($page) : $newsProductModel->select()->toArray();
    }

    /**
     * 查询新闻
     * @param $id
     * @param bool $show    fasle就是删除与没删除的都可以查到    true只查未删除的
     * @return array|null|\think\Model
     * $data times
     */
    public function getProductById($id,$show=false)
    {
        $newsProductModel = new \app\common\model\NewsProduct();
        if($show) $newsProductModel->where(['delete_time','>',0]);
        return $newsProductModel->where(['id'=>$id])->find()->toArray();
    }

    public function insertRes($data)
    {
        $insertData = [
            'title'=>$data['title'],
            'cate_id'=>$data['cate_id'],
            'pic'=>$data['pic'],
            'content'=>$data['content'],
            'sort'=>$data['sort'],
            'create_time'=>time(),
        ];

        return (new \app\common\model\NewsProduct())->add($insertData);
    }

    public function updateRes($data)
    {
        $updateData = [
            'title'=>$data['title'],
            'cate_id'=>$data['cate_id'],
            'pic'=>$data['pic'],
            'content'=>$data['content'],
            'sort'=>$data['sort'],
        ];
        return (new \app\common\model\NewsProduct())->modify($data['id'],$updateData);
    }

    public function softDelete($id)
    {
        return (new \app\common\model\NewsProduct())->softDelete($id);
    }
}