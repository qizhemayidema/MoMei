<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 17:05
 */

namespace app\common\tool;


use app\common\typeCode\CacheImpl;
use think\facade\Cache as CacheObj;

class Cache
{
    private $obj = null;

    public function __construct(CacheImpl $base)
    {
        $this->obj = $base;
    }

    public function setCache($data)
    {
        CacheObj::set($this->obj->getCacheName(),$data);
    }

    public function getCache()
    {
        return CacheObj::get($this->obj->getCacheName());
    }

    public function exists(): bool
    {
        return CacheObj::get($this->obj->getCacheName()) ? true : false;
    }

    public function clear(): string
    {
        CacheObj::set($this->obj->getCacheName(),null);
    }

}