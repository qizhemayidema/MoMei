<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/4
 * Time: 13:07
 */

namespace app\common\service;


class CinemaScreen
{
    public function getList($cinemaId,$page = null)
    {
        $handler = (new \app\common\model\CinemaScreen())->backgroundShowData()->where(['cinema_id'=>$cinemaId])
            ->order('sort','desc');

        return $page ? $handler->paginate($page) : $handler->select();
    }

    public function getListAll()
    {
        return (new \app\common\model\CinemaScreen())->backgroundShowData()->order('sort','desc')->select();
    }

    public function get($id)
    {
        return (new \app\common\model\CinemaScreen())->find($id);
    }

    public function insert($data)
    {
        $insert = [
            'cinema_id' => $data['cinema_id'],
            'name'      => $data['name'],
            'seat_sum'  => $data['seat_sum'],
            'sort'      => $data['sort'],
            'create_time' => time(),
        ];

        $model = (new \app\common\model\CinemaScreen());

        $model->insert($insert);

        return $model->getLastInsID();
    }

    public function update($id,$data)
    {
        $update = [
            'name'      => $data['name'],
            'seat_sum'  => $data['seat_sum'],
            'sort'      => $data['sort'],
        ];

        $model = (new \app\common\model\CinemaScreen());

        $model->where(['id'=>$id,'cinema_id'=>$data['cinema_id']])->update($update);
    }

    public function delete($id,$cinemaId)
    {
        (new \app\common\model\CinemaScreen())->where(['id'=>$id,'cinema_id'=>$cinemaId])->update([
            'delete_time' => time(),
        ]);
    }
}