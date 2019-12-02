<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 14:14
 */

namespace app\common\service;

use app\common\model\Cinema as CinemaModel;
class AssociatedCinema
{
    /**
     * 查询关联的影院
     * @param $type     int    资源方是影投 还是院线
     * @param $id       int    资源方的id
     * $data 2019/12/2 14:18
     */
    public function getAssociatedCinemaList($type,$id,$page=false)
    {
        $handler = new CinemaModel();
        if($type==1){
            $handler->where('tou_id','in',$id);
        }elseif ($type==2){
            $handler->where('yuan_id','in',$id);
        }
        return $page ? $handler->paginate($page) : $handler->select()->toArray();
    }

    public function getCinema($id)
    {
        return (new CinemaModel())->get($id);
    }
}