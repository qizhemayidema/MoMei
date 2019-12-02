<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/2
 * Time: 15:25
 */

namespace app\common\typeCode\menu;


use app\common\typeCode\CacheImpl;
use app\common\typeCode\MenuImpl;

class Cinema implements MenuImpl,CacheImpl
{
    private $masterType = 3;

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