<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/27
 * Time: 12:51
 */

namespace app\common\service;

use app\common\tool\Cache;
use app\common\typeCode\CacheImpl;
use app\common\typeCode\MenuImpl;
use app\common\model\Menu as MenuModel;

class Menu
{
    private $typeDesc = null;

    public function __construct(MenuImpl $menuImpl)
    {
        $this->typeDesc = $menuImpl;
    }

    public function get($id)
    {
        return (new MenuModel())->get($id);

    }

    public function getList()
    {
        $menuModel = new MenuModel();

        $data = [];

        if ($this->typeDesc instanceof CacheImpl){

            $cache = (new Cache($this->typeDesc));

            if ($cache->exists()){
                $data = $cache->getCache();
            }else{
                $data = $menuModel->getList($this->typeDesc->getMasterType(),$this->typeDesc->getMasterType());

                $cache->setCache($data);
            }
        }else{
            $data = $menuModel->getList($this->typeDesc->getMasterType(),$this->typeDesc->getMasterType());
        }

        return $data;
    }

    public function getListForPId($pId = 0)
    {
        $menuModel = new MenuModel();

        $data = $menuModel->getListForPId($this->typeDesc->getMasterType(),$pId);

        return $data;
    }

    public function insert(array $data)
    {
        $insert = [
            'master_type' => $this->typeDesc->getMasterType(),
            'p_id'        => $data['p_id'],
            'title'       => $data['title'],
            'icon'        => $data['icon'],
            'controller'  => $data['controller'],
            'action'      => $data['action'],
            'order'       => $data['order'] ?? 0,
        ];

        $menuModel = (new MenuModel());

        $menuModel->insert($insert);

        if ($this->typeDesc instanceof CacheImpl){
            (new Cache($this->typeDesc))->clear();
        }

        return $menuModel->getLastInsID();

    }

    public function update($id,array $data)
    {
        $update = [
            'p_id'        => $data['p_id'],
            'title'       => $data['title'],
            'icon'        => $data['icon'],
            'controller'  => $data['controller'],
            'action'      => $data['action'],
            'order'       => $data['order'] ?? 0,
        ];

        $menuModel = (new MenuModel());

        $menuModel->modify($id,$update);

        if ($this->typeDesc instanceof CacheImpl){
            (new Cache($this->typeDesc))->clear();
        }
    }

    public function delete($id)
    {
        $menuModel = new MenuModel();

        if((new MenuModel())->where(['p_id'=>$id])->find()){
            //判断是否有正在使用的pid
            throw new \Exception('此分类下还有子分类,无法删除');
        }

        $menuModel->where(['id'=>$id])->delete();

        if ($this->typeDesc instanceof CacheImpl){
            (new Cache($this->typeDesc))->clear();
        }
    }
}