<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 9:45
 */

namespace app\common\typeCode;


interface CacheImpl extends Base
{
    public function getName() : string;                     //获取缓存名称

    public function exists(string $key) : bool;             //缓存是否存在

    public function clear(string $key) : string;            //清除缓存
}