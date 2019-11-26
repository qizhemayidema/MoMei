<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/26
 * Time: 12:43
 */

namespace app\common\typeCode\role;

use app\common\typeCode\CacheImpl;
use app\common\typeCode\Role;

class M implements CacheImpl,Role
{
    private $cacheName = 'movie_role_type';
    private $type = 3;

    public function getCacheName() : string     //获取缓存名称
    {
        return $this->cacheName;
    }

    public function getRoleType():int  //获取类型
    {
        return $this->type;
    }
}