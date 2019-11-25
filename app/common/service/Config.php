<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 14:15
 */
namespace app\common\service;

class Config
{
    public function getList($path)
    {
        return (new \app\common\tool\Config($path))->getConfig('*');
    }
}