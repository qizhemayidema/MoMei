<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:14
 */


class Permission
{
    public function test()
    {
       $res =  (new \app\common\model\Permission())->get((new \app\common\typeCode\permission\B()),0,20);

    }
}