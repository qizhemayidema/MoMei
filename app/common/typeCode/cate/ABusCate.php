<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:19
 */
namespace app\common\typeCode\cate;

use think\facade\Cache;

class ABusCate implements \app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 1;

    private $cacheName = 'a_bus_cate';

    private $Level = 1;

    private $masterId = 0;

    public function getCateType(): int
    {
        return $this->type;
    }

    public function getLevelType(): int
    {
        return $this->Level;
    }

    public function getCacheName(): string
    {
        return $this->cacheName;
    }

    public function setMasterId($id)
    {
        $this->masterId = $id;
    }

    public function getMasterId(): int
    {

        return $this->masterId;
    }

    public function setCache($data)
    {
        Cache::set($this->getCacheName(),$data);
    }

    public function getCache()
    {
        return Cache::get($this->getCacheName());
    }

    public function exists(): bool
    {
        return Cache::get($this->getCacheName()) ? true : false;
    }

    public function clear(): string
    {
        Cache::set($this->getCacheName(),null);
    }
}