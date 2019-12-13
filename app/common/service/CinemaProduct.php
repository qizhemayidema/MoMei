<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/4
 * Time: 16:11
 */

namespace app\common\service;


use app\common\model\CinemaProductEntity;
use app\common\model\CinemaProductEntityStatus;
use app\common\model\Product;

class CinemaProduct
{
    private $groupCode;

    private $showType = true;     //true 面向后台数据  false 面向用户数据


    public function __construct($groupCode = null)
    {
        $this->groupCode = $groupCode;
    }

    public function setShowType($bool = true)
    {
        $this->showType = $bool;

        return $this;
    }

    public function get($id)
    {
        return (new \app\common\model\CinemaProduct())->where(['cinema_id'=>$this->groupCode])->find($id);
    }

    public function insert($data)
    {
        $insert = [
            'cinema_id'         => $this->groupCode,
            'cate_id'           => $data['cate_id'    ],
            'screen_id'         => $data['screen_id'  ],
            'level_id'          => $data['level_id'   ],
            'cate_name'         => $data['cate_name'  ],
            'cinema_name'       => $data['cinema_name'],
            'screen_name'       => $data['screen_name'],
            'level_name'        => $data['level_name' ],
            'entity_name'       => $data['entity_name'],
            'desc'              => $data['desc'],
//            'sort'              => $data['sort'       ],
            'price_json'        => $data['price_json' ],
            'price_month'       => $data['price_month'],
            'price_year'        => $data['price_year' ],
            'create_time'       => time(),
        ];

        $model = (new CinemaProductEntity());
        $model->insert($insert);

        return $model->getLastInsID();

    }

    public function update($id,$data)
    {
        $update = [
            'screen_id' => $data['screen_id'],
            'level_id'  => $data['level_id'],
            'cinema_name' => $data['cinema_name'],
            'level_name' => $data['level_name'],
            'entity_name'      => $data['entity_name'],
            'desc'      => $data['desc'],
            'screen_name' => $data['screen_name'],
            'cate_name' => $data['cate_name'],
            'price_json'        => $data['price_json' ],
            'price_month'       => $data['price_month'],
            'price_year'        => $data['price_year' ],
            'status'    => 2,
        ];

        \app\common\model\CinemaProductEntity::where(['id'=>$id])->update($update);

    }

    public function delete($entityId)
    {
        CinemaProductEntity::where(['id'=>$entityId,'cinema_id'=>$this->groupCode])->update(['delete_time'=>time()]);
    }

    public function changeStatus($id,$status)
    {

        (new \app\common\model\CinemaProductEntity())->where(['cinema_id'=>$this->groupCode,'id'=>$id])->update(['status'=>$status]);
    }


    //检查某个组合的数量
    public function getSum($cate_id,$level_id = 0,$screen_id = 0,$exceptId = [])
    {
        $where = [
            'cinema_id' => $this->groupCode,
            'cate_id' => $cate_id,
        ];

        $level_id && $where['level_id'] = $level_id;
        $screen_id && $where['screen_id'] = $screen_id;

        return (new CinemaProductEntity())->backgroundShowData()->where($where)->whereNotIn('id',$exceptId)->count();

    }


    /*-----------------------------------------------------*/


    public function getEntity($entityId)
    {
        return (new \app\common\model\CinemaProductEntity())->where(['cinema_id'=>$this->groupCode])->find($entityId);
    }

    public function getEntityList($page = null,$cateId = null,$levelId = null,$screenId = null)
    {
        $handler = (new CinemaProductEntity());

        $where = [];

        $cateId && $where['cate_id'] = $cateId;

        $levelId && $where['level_id'] = $levelId;

        $screenId && $where['screen_id'] = $screenId;

        $handler = $this->showType ? $handler->backgroundShowData() : $handler->receptionShowData();

        $where && $handler = $handler->where($where);

        $handler = $handler->where(['cinema_id'=>$this->groupCode])->order('sort','desc');


        return $page ? $handler->paginate($page) : $handler->select();
    }


/*------------------------------------------------------------------------------*/

    //如果不存在此日期则插入
    public function insertEntityDayPriceStatus($entityId,$dayJsonStr)
    {
        $insert = [];

        $date = (new CinemaProductEntityStatus())->where(['entity_id'=>$entityId])->column('date');

        $arr = json_decode($dayJsonStr,true);

        foreach ($arr as $key => $value){
            if (!in_array(strtotime($value['date']),$date)){
                $insert[] = [
                    'entity_id' => $entityId,
                    'date'      => strtotime($value['date']),
                    'status'    => 0,
                ];
            }
        }

        (new CinemaProductEntityStatus())->insertAll($insert);
    }


    public function updateEntityDayPriceStatus($entityId,$dayJsonStr)
    {
        $insert = [];

        (new CinemaProductEntity())->where(['entity_id'=>$entityId])->column('');

        $arr = json_decode($dayJsonStr);

        foreach ($arr as $key => $value){
            $insert[] = [
                'entity_id' => $entityId,
                'date'      => strtotime($value['date']),
                'status'    => 0,
            ];
        }

        (new CinemaProductEntityStatus())->insertAll($insert);
    }
}