<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/25
 * Time: 14:24
 */

namespace app\common\model;

use app\common\model\impl\BasicImpl;
use think\Model;
class Manager extends Model implements BasicImpl
{
    public function get($id) // 获取一条
    {
        return $this->where(['id'=>$id])->find();
    }

    public function add(Array $data) : int    //插入一条数据 返回自增主键id
    {
        return $this->insert($data);
    }

    public function modify($id,$data)      //更新一条数据
    {
        return $this->where(['id'=>$id])->update($data);
    }

    public function rm($id)            //删除一条数据
    {
        return $this->where(['id'=>$id])->delete();
    }

    public function softDelete($id)        //软删除
    {

    }
}