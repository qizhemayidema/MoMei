<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/27
 * Time: 11:47
 */

namespace app\common\typeCode\menu;

use app\common\typeCode\CacheImpl;
use app\common\typeCode\MenuImpl;

class B implements MenuImpl,CacheImpl
{
    private $masterType = 1;

    private $level = 2;

    private $cacheName = 'menu_b';


    public function getMasterType(): int
    {
        return $this->masterType;
    }

    public function getLevelType(): int
    {
        return $this->level;
    }

    public function getCacheName(): string
    {
        return $this->cacheName;
    }

}