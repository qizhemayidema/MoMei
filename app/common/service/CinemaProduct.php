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

    public function getList($page = null)
    {
        $handler = new \app\common\model\CinemaProduct();

        $handler = $handler->where('cinema_id',$this->groupCode)->order('id','desc');

        return $page ? $handler->paginate($page) : $handler->select();
     }

    public function insert($data)
    {
        $insert = [
            'cinema_id'         => $this->groupCode,
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

    public function update($id,$data)
    {
        $update = [
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
        ];

        \app\common\model\CinemaProduct::where(['id'=>$id])->update($update);

        //同步到entity
        $ids = CinemaProductEntity::where(['product_id'=>$id])->column('id');

        CinemaProductEntity::whereIn('id',$ids)->update([
            'screen_id'         => $data['screen_id'],
            'level_id'          => $data['level_id'],
            'cinema_name'       => $data['cinema_name'],
            'level_name'        => $data['level_name'],
            'screen_name'       => $data['screen_name'],
            'cate_name'         => $data['cinema_cate_name'],
            'product_name'      => $data['name'],
        ]);

    }

    public function changeStatus($id,$status)
    {

        (new \app\common\model\CinemaProduct())->where(['cinema_id'=>$this->groupCode,'id'=>$id])->update(['status'=>$status]);
    }


    /*-----------------------------------------------------*/
    public function insertEntity($data)
    {
        $insert = [
            'cinema_id'         => $this->groupCode,
            'cate_id'           => $data['cate_id'    ],
            'product_id'        => $data['product_id' ],
            'screen_id'         => $data['screen_id'  ],
            'level_id'          => $data['level_id'   ],
            'cate_name'         => $data['cate_name'  ],
            'cinema_name'       => $data['cinema_name'],
            'screen_name'       => $data['screen_name'],
            'level_name'        => $data['level_name' ],
            'product_name'      => $data['product_name'],
            'entity_name'       => $data['entity_name'],
            'sort'              => $data['sort'       ],
            'price_json'        => $data['price_json' ],
            'price_month'       => $data['price_month'],
            'price_year'        => $data['price_year' ],
            'create_time'       => time(),
        ];

        $model = (new CinemaProductEntity());
        $model->insert($insert);

        return $model->getLastInsID();
    }

    public function updateEntity($entityId,$data)
    {
        $update = [
            'screen_id'         => $data['screen_id'  ],
            'level_id'          => $data['level_id'   ],
            'cate_name'         => $data['cate_name'  ],
            'cinema_name'       => $data['cinema_name'],
            'screen_name'       => $data['screen_name'],
            'level_name'        => $data['level_name' ],
            'product_name'      => $data['product_name'],
            'entity_name'       => $data['entity_name'],
            'sort'              => $data['sort'       ],
            'price_json'        => $data['price_json' ],
            'price_month'       => $data['price_month'],
            'price_year'        => $data['price_year' ],
        ];

        $model = (new CinemaProductEntity());

        $model->where(['id'=>$entityId])->update($update);

    }

    public function getEntity($entityId)
    {
        return (new \app\common\model\CinemaProductEntity())->where(['cinema_id'=>$this->groupCode])->find($entityId);
    }

    public function getEntityList($cProductId)
    {
        $handler = (new CinemaProductEntity());

        $this->entityShowType ? $handler->backgroundShowData() : $handler->receptionShowData();

        return  $handler->where(['product_id'=>$cProductId,'cinema_id'=>$this->groupCode])->order('sort','desc')->select();
    }

    public function deleteEntity($entityId)
    {
        CinemaProductEntity::where(['id'=>$entityId,'cinema_id'=>$this->groupCode])->update(['delete_time'=>time()]);
    }
}