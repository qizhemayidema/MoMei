<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/26
 * Time: 13:35
 */

namespace app\common\typeCode\cate;


class LevelOption implements \app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 3;

    private $Level = 1;

    private $masterId = 0;

    private $cacheName = 'cate_level_option';

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