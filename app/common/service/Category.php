<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 16:07
 */

namespace app\common\service;


use app\common\tool\Cache;
use app\common\typeCode\CacheImpl;
use app\common\typeCode\cate\ABus;
use app\common\typeCode\CateImpl;

class Category
{
    public function getABusList()
    {
        return (new \app\common\model\Category())->getList(new ABus());
    }

    public function getOneById($id)
    {
        return (new \app\common\model\Category())->get($id);
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
            'is_show' => $data['is_show'] ?? 1,
        ];

        if($cateImpl instanceof CacheImpl){
            (new Cache($cateImpl))->clear();
        }
        return (new \app\common\model\Category())->add($result);


    }

    //修改一个分类
    public function update(CateImpl $cateImpl,$data)
    {
        $result = [
            'name'  => $data['name'],
            'p_id'  => $data['p_id'] ?? 0,
            'order_num' => $data['order_num'],
            'is_show' => $data['is_show'] ?? 1,
        ];

        (new \app\common\model\Category())->modify($data['id'],$result);

        if($cateImpl instanceof CacheImpl){
            (new Cache($cateImpl))->clear();
        }
    }

    //删除一个分类
    public function delete(CateImpl $cateImpl,$id)
    {
        (new \app\common\model\Category())->where(['id'=>$id])->delete();

        if($cateImpl instanceof CacheImpl){
            (new Cache($cateImpl))->clear();
        }
    }
}