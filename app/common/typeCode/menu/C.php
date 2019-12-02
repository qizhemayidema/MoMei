<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/12/2
 * Time: 11:04
 */

namespace app\common\typeCode\menu;

use app\common\typeCode\CacheImpl;
use app\common\typeCode\MenuImpl;
class C implements MenuImpl,CacheImpl
{
    private $masterType = 2;

    private $level = 2;

    private $cacheName = 'menu_c';


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