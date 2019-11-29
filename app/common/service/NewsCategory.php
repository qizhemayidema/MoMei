<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 14:25
 */

namespace app\common\service;


use app\common\tool\Cache;
use app\common\typeCode\CacheImpl;

class NewsCategory
{
    /**
     * 查询出来全部未删除的才进行花奴才能
     * @param \app\common\typeCode\NewsCategory\NewsCateGory $obj
     * @param bool $del     查询删除的还是未删除的   true 未删除的   false删除的
     * @param bool $page    false 不分页   传值过来进行分页
     * $data 2019/11/28 15:00
     */
    public function getNewsCategoryLists(\app\common\typeCode\NewsCategory\NewsCateGory $obj,$del = true,$page = false){
        $newsCategoryModel = new \app\common\model\NewsCategory();

        $level = $obj->getLevelType();

        $handler = $del?$newsCategoryModel->where(['delete_time'=>0])->order('order_num desc'):$newsCategoryModel->where(['delete_time','>',0])->order('order_num desc');

        if($obj instanceof CacheImpl && !$page && $del){
            $cache = new Cache($obj);
            if($result = $cache->getCache()){
                return $result;
            }else{
                //查询对应类型的数据全部的
                $data  = $handler->select()->toArray();
                $result = $this->getMoreList($data,$level);
                $cache->setCache($result);
                return $result;
            }
        }else{
            return $page?$handler->paginate($page):$handler->select()->toArray();
        }
    }

    public function getNewsCategoryAllLists()
    {
        $newsCategoryModel = new \app\common\model\NewsCategory();
        return $newsCategoryModel->select()->toArray();
    }

    /**
     * 根据类别查询
     * @param int $pId
     * @param null $page    null不分页
     * @return \think\Collection|\think\Paginator
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * $data 2019/11/28 16:50
     */
    public function getListByPId($pId = 0,$page = Null)
    {
        $areaModel = new \app\common\model\NewsCategory();

        $handler = $areaModel->where(['pid'=>$pId]);

        return $page ? $handler->paginate($page) : $handler->select();

    }

    public function insert(CacheImpl $cacheImpl,$data)
    {
        $updateData = [
            'name'=>$data['name'],
            'type'=>$data['type'],
            'pid'=>$data['pid'],
            'order_num'=>$data['order_num'],
        ];

        $result = (new \app\common\model\NewsCategory())->add($updateData);

        if(!$result) return false;

        if($cacheImpl instanceof  CacheImpl){
            (new Cache($cacheImpl))->clear();
        }

        return true;
    }

    public function getFindRes($id)
    {
        return (new \app\common\model\NewsCategory())->get($id);
    }

    public function updateRes(CacheImpl $cacheImpl,$data)
    {
        $updateData = [
            'name'=>$data['name'],
            'type'=>$data['type'],
            'pid'=>$data['pid'],
            'order_num'=>$data['order_num'],
        ];

        $result = (new \app\common\model\NewsCategory())->modify($data['id'],$updateData);

        if(!$result) return false;

        if($cacheImpl instanceof CacheImpl){
            (new Cache($cacheImpl))->clear();
        }

        return true;
    }

    public function softDelete(CacheImpl $cacheImpl,$id)
    {
        $result = (new \app\common\model\NewsCategory())->softDelete($id);

        if(!$result) return false;

        if($cacheImpl instanceof CacheImpl){
            (new Cache($cacheImpl))->clear();
        }

        return true;
    }

    //递归放入下级
    private function getMoreList($categorys,$max = 1,$pId = 0,$l = 0)
    {
        $list = [];

        foreach ($categorys as $k=>$v){

            if ($v['pid'] == $pId){
                unset($categorys[$k]);
                if ($l < $max){
                    //小于三级
                    $v['children'] = $this->getMoreList($categorys,$max,$v['id'],$l+1);
                }
                $list[] = $v;
            }
        }
        return $list;
    }
}