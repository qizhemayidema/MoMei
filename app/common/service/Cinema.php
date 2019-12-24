<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/10
 * Time: 11:12
 */

namespace app\common\service;

use app\common\model\Manager as ManagerModel;
use app\common\model\ManagerInfo as ManagerInfoModel;
use app\Request;


//有关影院的业务
class Cinema extends Manager
{
    private $attrIds = [];

    public function setAttrIds($attrIds)
    {
        $this->attrIds = $attrIds;

        return $this;
    }

    public function getList($page = null)
    {
        $handler = new \app\common\model\Manager();

        $managerInfo = $this->getManagerImpl();

        $showType = $this->getShowType();

        $alias = 'manager';

        $handler = $showType ? $handler->backgroundShowData($alias) : $handler->receptionShowData($alias);

        $handler = $handler->alias($alias)->join('manager_info info','manager.id = info.master_user_id')
            ->where(['info.type'=>$managerInfo->getManagerType()])
            ->field('manager.group_code,info.*,info.id none_id,'.$alias.'.*');


        $handler = count($this->order) ? $handler->order($alias.'.'.$this->order[0],$this->order[1]) : $handler;


        if ($this->attrIds && count($this->attrIds)){
            $handler = $handler->join('category_obj_have_attr attr','info.master_user_id = attr.object_id and attr.type = 1');

            $handler = $handler->whereIn('attr.attr_id',$this->attrIds);
        }

        return $page ? $handler->paginate(['list_rows'=>$page,'query'=>request()->param()]) : $handler->select();

    }

    public function getListByAttr($attrIds,$isReal = true)      //如果 real 为真则attrIds需要全部符合,如果为假,则符合其中一个值即可
    {
        $handler = new \app\common\model\Manager();

        $managerInfo = $this->getManagerImpl();

        $showType = $this->getShowType();

        $alias = 'manager';

        $length = count($attrIds);

        $handler = $showType ? $handler->backgroundShowData($alias) : $handler->receptionShowData($alias);

        $handler = $handler->alias($alias)->join('manager_info info','manager.id = info.master_user_id')
            ->where(['info.type'=>$managerInfo->getManagerType()])
            ->field('manager.group_code,info.*,info.id none_id,'.$alias.'.*');

        $handler = count($this->order) ? $handler->order($alias.'.'.$this->order[0],$this->order[1]) : $handler;


        if ($attrIds && $length){
            if ($isReal){
                $handler = $handler->where('info.master_user_id', 'in', function ($query) use ($attrIds, $length) {
                    //查询出拥有特定属性的云库ID
                    $query->name('category_obj_have_attr')->field('object_id')
                        ->where('attr_id', 'in', $attrIds)->group('object_id')->having('count(attr_id)=' . $length);
                });
            }else{
                $handler = $handler->join('category_obj_have_attr attr','info.master_user_id = attr.object_id and attr.type = 1');

                $handler = $handler->whereIn('attr.attr_id',$attrIds);
            }
        }

        return $handler->paginate(['list_rows'=>15,'query'=>request()->param()]);

    }

    public function getSumByCity($cityIds = [])
    {
        $managerInfo = $this->getManagerImpl();

//        $showType = $this->getShowType();

        $handler = new ManagerInfoModel();

//        $alias = 'cinema';


        $handler = $handler->alias('info')->join('manager manager','manager.id = info.master_user_id')
            ->where(['manager.delete_time'=>0])
            ->where(['manager.status'=>1])
            ->where(['info.type'=>$managerInfo->getManagerType()])
            ->whereIn('city_id',$cityIds);

        if ($cityIds){
            $handler = $handler->whereIn('info.city_id',$cityIds);
        }
        return $handler->count();
    }
}