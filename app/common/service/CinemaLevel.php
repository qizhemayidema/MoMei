<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/2
 * Time: 17:24
 */

namespace app\common\service;


class CinemaLevel
{

    public function insert($cinemaId,$levels,$options)
    {
        $model = new \app\common\model\CinemaLevel();

        $insert = [];
        foreach ($levels as $key => $value)     // key 选项名 id  value 选项名的字符串
        {
            $option = explode('-',$options[$key]);
            $insert[] = [
                'cinema_id' => $cinemaId,
                'name_id' => $key,
                'value_id' => $option[0],
                'name' => $value,
                'value' => $option[1]
            ];
        }

        (new \app\common\model\CinemaLevel())->insertAll($insert);
    }

    //更改 如果不存在则新增
    public function update($cinemaId,$levels,$options)
    {
        $model = new \app\common\model\CinemaLevel();

        foreach ($levels as $key => $value)     // key 选项名 id  value 选项名的字符串
        {
            $option = explode('-',$options[$key]);

            $id = $model->where(['cinema_id'=>$cinemaId,'name_id'=>$key])->value('id');

            if ($id){
                $model->where(['cinema_id'=>$cinemaId,'name_id'=>$key])->update([
                    'value_id' => $option[0],
                    'name' => $value,
                    'value' => $option[1]
                ]);
            }else{
                $model->insert([
                    'cinema_id' => $cinemaId,
                    'name_id' => $key,
                    'value_id' => $option[0],
                    'name' => $value,
                    'value' => $option[1]
                ]);
            }


        }
    }


    public function getIdColumns($cinemaId)
    {
        return (new \app\common\model\CinemaLevel())->where(['cinema_id'=>$cinemaId])->column('value_id','name_id');
    }
//    public function update($id)
}