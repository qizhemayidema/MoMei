<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/11/25
 * Time: 12:19
 */

class ABusinessCate implements \app\common\typeCode\Base,\app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 1;

    private $cacheName = 'aa';

    public $where = [];

    public function getCateType(): int
    {
        // TODO: Implement getCateType() method.
        return $this->type;
    }

    public function getLevelType(): int
    {
        // TODO: Implement getLevelType() method.
    }

    public function getName(): string
    {
        // TODO: Implement getName() method.
        return $this->cacheName;
    }
}