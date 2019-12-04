<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/4
 * Time: 16:11
 */

namespace app\common\service;


use app\common\model\CinemaProductEntity;

class CinemaProduct
{
    private $groupCode;

    private $entityShowType = true;     //true 面向后台数据  false 面向用户数据

    public function __construct($groupCode = null)
    {
        $this->groupCode = $groupCode;
    }

    public function setEntityShowType($bool = true)
    {
        $this->entityShowType = $bool;

        return $this;
    }

    public function get($id)
    {
        return (new \app\common\model\CinemaProduct())->where(['cinema_id'=>$this->groupCode])->find($id);
    }

    public function getList($groupCode = null,$page = null)
    {
        $groupCode = $groupCode ?? $this->groupCode;

        $handler = new \app\common\model\CinemaProduct();

        $handler->where(['cinema_id'=>$groupCode])->order('id','desc');

        return $page ? $handler->paginate($page) : $handler->select();
     }

    public function insert($data)
    {
        $insert = [
            'cinema_id'         => $data['cinema_id'],
            'cate_id'           => $data['cate_id'],
            'screen_id'         => $data['screen_id'],
            'level_id'          => $data['level_id'],
            'cinema_name'       => $data['cinema_name'],
            'level_name'        => $data['level_name'],
            'name'              => $data['name'],
            'desc'              => $data['desc'],
            'screen_name'       => $data['screen_name'],
            'cinema_cate_name'  => $data['cinema_cate_name'],
            'type'              => $data['type'],
            'select_max_sum'    => $data['select_max_sum'  ],
            'sum'               => $data['sum'],
            'status'            => $data['status'],
            'create_time'       => time(),
        ];
        $model = (new \app\common\model\CinemaProduct());

        $model->insert($insert);

        return $model->getLastSql();

    }

    public function changeStatus($id,$status)
    {
        (new \app\common\model\CinemaProduct())->modify($id,['status'=>$status]);
    }

    public function getEntity($entityId)
    {
        return (new \app\common\model\CinemaProductEntity())->where(['cinema_id'=>$this->groupCode])->find($entityId);
    }

    public function getEntityList($cProductId)
    {
        $handler = (new CinemaProductEntity());

        $this->entityShowType ? $handler->backgroundShowData() : $handler->receptionShowData();
        return  $handler->where(['id'=>$cProductId,'cinema_id'=>$this->groupCode])->order('sort','desc')->select();
    }
}