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
    public function getCacheName() : string;     //获取缓存名称
    
    public function exists() : bool;             //缓存是否存在

    public function setCache($data);             //设置缓存

    public function getCache();              //获取缓存

    public function clear() : string;            //清除缓存
}