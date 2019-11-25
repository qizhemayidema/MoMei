<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:42
 */
namespace app\common\typeCode\permission;

use app\common\typeCode\CacheImpl;

class B implements CacheImpl
{
    public $where = [];
    public $cacheName = 'platform_type';

    public function getCacheName(): string
    {
        // TODO: Implement getCacheName() method.
        return $this->cacheName;
    }

    public function getCache()
    {
        // TODO: Implement getCache() method.
    }

    public function setCache($data)
    {
        // TODO: Implement setCache() method.
    }

    public function exists(string $key): bool
    {
        return true;
        // TODO: Implement exists() method.
    }

    public function clear(string $key): string
    {
        return '1';
        // TODO: Implement clear() method.
    }
}