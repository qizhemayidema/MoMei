<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 16:07
 */

namespace app\common\service;


use app\common\model\CategoryAttr;
use app\common\model\CategoryObjHaveAttr;
use app\common\tool\Cache;
use app\common\typeCode\CacheImpl;
use app\common\typeCode\cate\ABus;
use app\common\typeCode\cate\Product as ProductCateTypeCode;
use app\common\typeCode\CateImpl;
use app\common\model\Category as CateModel;

class Category
{
    //获取一条数据
    public function get($id)
    {
        $cateModel = new CateModel();

       return $cateModel->find($id);
    }

    //获取列表
    public function getList(CateImpl $cateImpl)
    {
        $cateModel = new CateModel();

        $masterId = $cateImpl->getMasterId();

        $level = $cateImpl->getLevelType();

        $type = $cateImpl->getCateType();

        if ($cateImpl instanceof CacheImpl){

            $cache = (new Cache($cateImpl));

            if ($cache->exists()) return $cache->getCache();

            $result = $cateModel->getList($type,$level,$masterId);

            $cache->setCache($result);

            return $result;
        }

        return (new CateModel())->getList($type,$level,$masterId);
    }

    //获取一条数据通过id
    public function getOneById($id)
    {
        return (new CateModel())->get($id);
    }

    public function getListByType($type)
    {
        return (new CateModel())->getList($type);
    }

    public function getListByPId(CateImpl $cateImpl,$pId = 0)
    {
        return (new CateModel())->where(['p_id'=>$pId,'type'=>$cateImpl->getCateType()])->select();
    }

    //添加一个分类
    public function insert(CateImpl $cateImpl,$data)
    {
        $result = [
            'master_id' => $cateImpl->getMasterId(),
            'p_id'  => $data['p_id'] ?? 0,
            'type'  => $cateImpl->getCateType(),
            'name'  => $data['name'],
            'order_num' => $data['order_num'],
            'icon'  => $data['icon'] ?? '',
            'is_show' => $data['is_show'] ?? 1,
        ];

        if($cateImpl instanceof CacheImpl){

            (new Cache($cateImpl))->clear();

        }

        return (new CateModel())->add($result);
    }

    //修改一个分类
    public function update(CateImpl $cateImpl,$data)
    {
        $result = [
            'name'  => $data['name'],
            'p_id'  => $data['p_id'] ?? 0,
            'order_num' => $data['order_num'],
            'icon'  => $data['icon'] ?? '',
            'is_show' => $data['is_show'] ?? 1,
        ];

        (new CateModel())->modify($data['id'],$result);

        if($cateImpl instanceof CacheImpl){
            (new Cache($cateImpl))->clear();
        }
    }

    //删除一个分类
    public function delete(CateImpl $cateImpl,$id)
    {
        (new CateModel())->where(['id'=>$id])->delete();

        if($cateImpl instanceof CacheImpl){
            (new Cache($cateImpl))->clear();
        }
    }

    //判断分类是否正在被某个对象使用

    /*------------------attr--------------------------*/

    public function getAttr($attrId)
    {
        return (new CategoryAttr())->find($attrId);
    }

    public function getAttrList($cateId)
    {
        return (new CategoryAttr())->where(['cate_id'=>$cateId])
            ->order('order_num','desc')
            ->select();
    }

    //添加一个分类属性
    public function insertAttr($cateId,$data)
    {
        $result = [
            'cate_id' => $cateId,
            'value'  => $data['value'],
            'order_num' => $data['order_num'],
            'is_show' => $data['is_show'] ?? 1,
        ];

        return (new CategoryAttr())->add($result);
    }

    //修改一个分类属性
    public function updateAttr($attrId,$data)
    {
        $result = [
            'value'  => $data['value'],
            'order_num' => $data['order_num'],
            'is_show' => $data['is_show'] ?? 1,
        ];

        return (new CategoryAttr())->modify($attrId,$result);
    }

    //删除一个分类属性
    public function deleteAttrById($attrId)
    {
        (new CategoryAttr())->where(['id'=>$attrId])->delete();

        (new CategoryObjHaveAttr())->where(['attr_id'=>$attrId])->delete();
    }

    //删除多个分类属性
    public function deleteAttrByCateId($cateId,$exceptAttrIds = [])
    {
        (new CategoryAttr())->where(['cate_id'=>$cateId])->whereNotIn('id',$exceptAttrIds)->delete();

        (new CategoryObjHaveAttr())->where(['cate_id'=>$cateId])->whereNotIn('attr_id',$exceptAttrIds)->delete();
    }

    /**
     * 获取某影院的产品分类
     * @param $cinemaId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 16/1/2020 上午10:31
     */
    public function getProcudtCate($cinemaId)
    {
        $cateModel = new CateModel();

        $productCateTypeCode = (new ProductCateTypeCode());


        $result = $cateModel->where(['type' => $productCateTypeCode->getCateType()])->field('id,name')
            ->whereIn('id', function ($query) use ($cinemaId) {
                //查询出拥有特定属性的云库ID
                $query->name('cinema_product')->field('cate_id')->where(['cinema_id' => $cinemaId])
                    ->group('cate_id');
            })->select()->toArray();

        return $result;
    }
}