<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:42
 */
namespace app\common\typeCode\permission;

use app\common\typeCode\CacheImpl;
use app\common\typeCode\PermissionImpl;

class B implements CacheImpl,PermissionImpl
{
    private $cacheName = 'platform_permission_type';
    private $type = 1;

    public function getCacheName(): string
    {
        // TODO: Implement getCacheName() method.
        return $this->cacheName;
    }

    public function getPermissionType(): int
    {
        // TODO: Implement getPermissionType() method.
        return $this->type;
    }
}