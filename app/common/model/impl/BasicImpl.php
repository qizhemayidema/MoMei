<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 10:08
 */

namespace app\common\model\impl;


interface BasicImpl
{

    public function get($id); // 获取一条

    public function getList(\app\common\typeCode\Base $base,$start,$length);  // 获取list

    public function add(array $data) : int;    //添加一条数据 返回自增主键id

    public function modify($id,$data);      //更新一条数据

    public function rm($id);            //删除一条数据

    public function softDelete($id);        //软删除

}