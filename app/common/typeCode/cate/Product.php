<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 14:31
 */

namespace app\common\typeCode\cate;


class Product implements \app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 6;

    private $Level = 2;

    private $masterId = 0;

    private $cacheName = 'cate_product';

    private $issetAttr = false;

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

    public function issetAttr(): bool
    {
        return $this->issetAttr;
    }

}