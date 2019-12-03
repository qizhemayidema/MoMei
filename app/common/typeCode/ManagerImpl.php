<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/3
 * Time: 11:16
 */

namespace app\common\typeCode;


interface ManagerImpl extends BaseImpl
{
    public function getManagerType();   //获取类型

    public function isInfo();       //是否需要info

    public function getInfoField();     //获取字段名称 一维数组
}