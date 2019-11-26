<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:14
 */
namespace app\common\service;

use app\common\typeCode\permission\B;

class Permission
{
    /**
     * 查询全部的权限
     * $data 2019/11/25 17:36
     */
    public function getRuleList()
    {
       return  (new \app\common\model\Permission())->getList((new B()));
    }
}