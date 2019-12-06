<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/6
 * Time: 11:59
 */

namespace app\common\service;

use app\common\model\AreaConfig as AreaConfigModel;
class AreaConfig
{
    private $pageLength = null;

    public function pageLength($pageLength = 15)
    {
        $this->pageLength = $pageLength;

        return $this;
    }

    public function getList($hot = false)
    {
        $handler = new AreaConfigModel();
        $handler = $handler->alias('a')->join('area b','a.city_id=b.id');
        if($hot) $handler = $handler->where('is_hot',$hot);
        return $this->pageLength ? $handler->paginate($this->pageLength) : $handler->select()->toArray();
    }

    public function updateNotInAll($ids,$data){
        $handler = new AreaConfigModel();
        return $handler->whereNotIn('city_id',$ids)->update($data);
    }

    public function insertAll($ids)
    {
        $handler = new AreaConfigModel();
        return $handler->insertAll($ids);
    }

    public function updateInAll($ids,$data)
    {
        $handler = new AreaConfigModel();
        return $handler->whereIn('city_id',$ids)->update($data);
    }
}