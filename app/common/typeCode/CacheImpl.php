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

}