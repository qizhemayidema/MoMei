<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 14:25
 */

namespace app\common\service;


use app\common\tool\Cache;

class NewsCategory
{
    /**
     * @param \app\common\typeCode\NewsCategory\NewsCateGory $obj
     * @param bool $del     查询删除的还是未删除的   true 未删除的   false删除的
     * @param bool $page    false 不分页   传值过来进行分页
     * $data 2019/11/28 15:00
     */
    public function getNewsCategoryLists(\app\common\typeCode\NewsCategory\NewsCateGory $obj,$del = true,$page = false){
        $newsCategoryModel = new \app\common\model\NewsCategory();

        $level = $obj->getLevelType();

        $handler = $del?$newsCategoryModel->where(['delete_time'=>0]):$newsCategoryModel->where(['delete_time','>',0]);

        if($obj instanceof Cache && !$page){
            $cache = new Cache($obj);
            if($result = $cache->getCache()){
                return $result;
            }else{
                //查询对应类型的数据全部的
                $data  = $handler->select();;
                $result = $this->getMoreList($data,$level);
                $cache->setCache($result);
                return $result;
            }
        }else{
            return $page?$handler->paginate($page):$handler->select();
        }
    }

    private function getMoreList($categorys,$max = 1,$pId = 0,$l = 0)
    {
        $list = [];

        foreach ($categorys as $k=>$v){

            if ($v['p_id'] == $pId){
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