<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:19
 */
namespace app\common\typeCode\cate;

class ABus implements \app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 1;

    private $Level = 1;

    private $masterId = 0;

    private $cacheName = 'cate_a_bus';

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
        // TODO: Implement issetAttr() method.
        return $this->issetAttr;
    }


}