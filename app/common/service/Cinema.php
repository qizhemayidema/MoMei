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


//有关影院的业务
class Cinema extends Manager
{
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