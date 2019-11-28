<?php
/**
 * Created by PhpStorm.
 * User: fk
 * Date: 2019/11/28
 * Time: 14:34
 */

namespace app\common\typeCode\NewsCategory;


use app\common\typeCode\CacheImpl;

class NewsCateGory implements CacheImpl,\app\common\typeCode\NewsCategoryImpl
{
    private $cacheName = "news_category";
    private $levelType = 1;
    public function getCacheName(): string
    {
        // TODO: Implement getCacheName() method.
        return $this->cacheName;
    }

    public function getLevelType(): int
    {
        // TODO: Implement getLevelType() method.
        return $this->levelType;
    }


}