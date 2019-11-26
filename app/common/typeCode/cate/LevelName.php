<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 14:10
 */

namespace app\common\typeCode\cate;


class LevelName implements \app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 5;

    private $Level = 1;

    private $masterId = 0;

    private $cacheName = 'cate_level_name';

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

}