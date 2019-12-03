<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 18:54
 */

namespace app\common\typeCode\cate;


class CBus implements \app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 2;

    private $cacheName = 'cate_c_bus';

    private $Level = 1;

    private $masterId = 0;

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

    /**
     * @return bool
     */
    public function issetAttr(): bool
    {
        return $this->issetAttr;
    }


}